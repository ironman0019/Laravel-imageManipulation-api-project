## Laravel Image Manipulation REST API

## Description
Simple image resize REST API project with Laravel. You can send your image with url or uploaded file, then set the width and height 
of your image and get the resized image. with url you send the width and height as url params. the height is optional but width is nessesry.

## Installation
1. Clone the project
2. Navigate to the project root directory using command line
3. Run `composer install`
4. Copy `.env.example` into `.env` file
5. Adjust `DB_*` parameters.
6. Run `php artisan key:generate --ansi`
7. Run `php artisan migrate`
8. Run `npm install`
9. Run `npm run dev`
### Useing postman
Run `php artisan serve` and use postman to test the api.
for example : http://localhost:8000/api/v1/album => this will return all images albums.

### Note
1. You nead to open the project in browser and login and get the auth token and pass it to postman as bearer token.
2. You can read the api docs at `/docs/api` url.

## Screenshots
![Alt text](/public/screenshots/1.jpg?raw=true "Optional Title")
<br>
![Alt text](/public/screenshots/2.jpg?raw=true "Optional Title")
<br>
![Alt text](/public/screenshots/3.jpg?raw=true "Optional Title")
