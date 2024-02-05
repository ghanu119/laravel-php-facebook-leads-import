## Facebook SDK

Need to add the facebbook php business SDK into your package.

```composer require facebook/php-business-sdk```

## Log channel

You can define separate log channel inside the config folder as Log system provided in the Laravel framework. If you have any other then you can remove it.

## Verify CSRF Token

If you want to put in a ```web.php``` file then you have to except the URL in CSRFTokenMiddleware file inside your laravel app.

## Verify the URL in facebook app console.

- Inside the Facebook app console, before adding the URL they must verify the URL in the live URL by putting the our random encrypted string into it and that must be returned from the our request.
- You can see the **WebhoookController.php** where in the starting of the file there is a **hub_challenge** and **hub_verify_token** that we will received in our request payload.
- Then in the **hub_verify_token** Facebook will add our custom random encrypted string and that we will match and confirm that facebook requested in our system.
- If verification success then facebook will add the URL as webhook for the leads into their system.


## Facebook App related 

You can open and check the **FacebookLeadService.php** file and where I added the steps to get the setup step by step.

