# 🖼️ Image Uploading and Avatar

## Prerequisites

Users should have a possibility to upload their own images and use them as avatar.

## Image uploading

To upload image user should call **POST** [/images](https://localhost/docs#/User/api_images_post)

In response user will get `id` which can be used to set avatar.

To set avatar user should call **PUT** [/users/me/avatar](https://localhost/docs#/User/api_usersmeavatar_put)

> To upload image user should be [authorized](./authorization.md)
