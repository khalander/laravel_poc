Jwt
1) 4 ways we can pass token
-----------------------------------------------------------------
Passport:

Ref: 
http://www.bubblecode.net/en/2016/01/22/understanding-oauth2/
https://www.youtube.com/watch?v=UKSQdg1uPbQ&list=PL1TrjkMQ8UbWqLEiPjsmuoTJNLKbcsYce&index=1

Tips:
login/Create token: $user->createToken('Auth Token')->accessToken;
logout/delete token:  $user->tokens()->delete();
Token Scopes: to restrict the routes (permission)
expire setting: under auth service provider, Passport::tokenExepriesIn()

-----------------------------------------------------------------