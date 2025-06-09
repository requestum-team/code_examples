# 🔑 Authorization

## Prerequisites

* Postman or any other http client for testing api

## Registration

For registration application provide separate endpoint: **POST** [/register](https://localhost/docs#/Auth/api_register_post).

## Login

By default application have user fixtures with credentials:

* user1
  * username
    * user1@example.com
  * password
    * password1
* user2
  * username
      * user2@example.com
  * password
      * password2
* user3
  * username
      * user3@example.com
  * password
      * password3

For login it should be used **POST** [/login](https://localhost/docs#/Login%20Check/login_check_post)

In response we get: 

```json
{
    "token": "!!token!!",
    "refreshToken": "!!refreshToken!!",
    "is2faEnabled": false
}
```

Explanation:

* **token** - is JWT token which contains user identifier and roles.
* **refreshToken** - is token which can be user in **POST** [/token/refresh](https://localhost/docs#/Auth/api_tokenrefresh_post).
* **is2faEnabled** - is flag that answer question if [Two Factor Authentication](#two-factor-authentication) is enabled.
  * if `true` then **refreshToken** will be empty and **token** will be usable only for [Two Factor Authentication](#two-factor-authentication)

## Refresh

Using JWT token has some advantages and disadvantages. 
JWT token live shot amount of time and expect that user will login each time is bad practice. 
To resolve this issue refresh token should be introduced.

To refresh user jwt token we need to call **POST** [/token/refresh](https://localhost/docs#/Auth/api_tokenrefresh_post)

## Two Factor Authentication

**Two Factor Authentication** is a good way to increase security of user.

### Enabling

By default it's disabled for each new registered user but it can be enabled by calling **POST** [/2fa/enable](https://localhost/docs#/2FA/api_2faenable_post)
In response user will get qrcode img url, so it can be scanned by Authenticator app (such as Google Authenticator) and registered in it.

```json
{
    "qrUrl": "!!url!!",
    "secret": "!!secret!!"
}
```

> 🚨 User should be authorized before enabling **Two Factor Authentication**

### Verification

For verification process we need to start with [Login](#login) process. 
If Two Factor Authentication is [enabled](#enabling), login response will return slightly different body:
```json
{
    "is2faEnabled": true,
    "token": "!!temporary token for verification!!",
    "refreshToken": null
}
```

Explanation:

* **is2faEnabled** - is true because it's enabled.
* **token** - is containing temporary token for verification process.
* **refreshToken** - is `null` because user is not fully authorized yet.

For Verification user need to call **POST** [/2fa/verify](https://localhost/docs#/2FA/api_2faverify_post)

Body:
```json
{
    "totpCode": "!!code!!",
    "token": "!!token!!"
}
```

Explanation:

* **totpCode** - is a code from authenticator.
* **token** - is a temporary token from login endpoint.

As a response for verification user will get new JWT token and Refresh token.

Response:

```json
{
    "token": "!!token!!",
    "refreshToken": "!!refresh token!!"
}
```

## Get Current user

To get current user data,
user need to send request with `Authorization: Bearer !!jwt_token!!` header to **GET** [/users/me](https://localhost/docs#/User/api_usersme_get).
