<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\WebhookSignature;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class CrowdfundingController extends Controller
{
    public function intent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'project_id' => 'required|numeric|exists:projects,id',
        ]);

        $payment = $request->user()->payWith(
            $request->amount,
            ['card', 'bancontact'],
            [
                'payment_method_types' => ['card', 'bancontact'],
                'metadata' => [
                    'user_id' => $request->user()->id,
                    'project_id' => $request->project_id,
                    'amount' => $request->amount,
                    'currency' => 'EUR',
                ],
            ]
        );

        return $payment;
    }

    public function webhook(Request $request)
    {
        try {
            WebhookSignature::verifyHeader(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('app.webhook.secret'),
                config('app.webhook.tolerance')
            );

            $event = $request->type;
            $payment = $request['data']['object'];

            if ($event === 'payment_intent.succeeded') {
                $user = User::find($payment['metadata']['user_id']);
                $project = Project::find($payment['metadata']['project_id']);
                $amount = $payment['metadata']['amount'] / 100;
                $project->crowdfunding()->update([
                    'amount' => $project->crowdfunding->amount + $amount,
                ]);
            }
        } catch (SignatureVerificationException $exception) {
            return response()->json([
                'error' => 'Invalid signature',
            ], 403);
        }
    }
}
