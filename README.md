<p align="center"><a href="https://comhype.herokuapp.com" target="_blank"><img src="https://dantindurand.fr/img/comhype.png" width="400"></a></p>

<p align="center">
<a href="#"><img src="https://img.shields.io/badge/Contributors-1-green?style=plastic&logo=github" alt="Contributors"></a>
<a href="#"><img src="https://img.shields.io/badge/Version-0.1.2-green?style=plastic" alt="Version"></a>
<a href="#"><img src="https://img.shields.io/badge/Branches-1-white?style=plastic" alt="Branches"></a>
</p>

## 💡 About Comhype

Comhype is a crowdfunding mobile application allowing project leaders to get a first feedback on their project.

## ⚙️ Installation

1. clone the repository `git clone git@github.com:com-hype/api.git`
2. cd into the directory `cd api`
3. create a `.env` file
4. install dependencies `composer install`
5. run `php artisan migrate`
6. run `php artisan db:seed`
7. run `php artisan serve`

## ❌ Errors messages

| Error                            | Description                             |
| -------------------------------- | --------------------------------------- |
| USERNAME_ALREADY_EXISTS          | The username is already used            |
| USER_ALREADY_REGISTERED          | The user is already registered          |
| EMAIL_ALREADY_EXISTS             | The email is already used               |
| DEVICE_NAME_REQUIRED             | The device name is required             |
| PASSWORD_TOO_SHORT               | The password is too short               |
| INVALID_BIRTHDATE                | The birthdate is invalid                |
| WRONG_CREDENTIALS                | The credentials are wrong               |
| USER_NOT_AUTHENTICATED           | The user is not authenticated           |
| USER_NOT_ALLOWED                 | The user is not allowed                 |
| PROFILE_IMAGE_UPLOAD_NOT_ALLOWED | The profile image upload is not allowed |
| USER_NOT_REGISTERED_AS_PROJECT   | The user is not registered as project   |
| USER_CANNOT_LIKE_HIMSELF         | The user cannot like himself            |
| USER_ALREADY_LIKED_THIS_PROJECT  | The user already liked this project     |
| USER_CANNOT_EDIT_THIS_PROJECT    | The user cannot edit this project       |

## 🔗 Links

-   [📚 API Documentation](https://dantin.stoplight.io/docs/comhype)
-   [🚀 API Production](https://comhype.herokuapp.com)

## 📝 Crédits

Developed by [@dantin-durand](https://github.com/dantin-durand)
