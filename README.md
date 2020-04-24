# me like! ▲

![Demo](docs/like.gif)

## What does it do? 

This bundle provides a widget that users can click on to like things (backed by an API). 

Likes need to be confirmed in order to count - this is accomplished by asking for an email address and sending an email
with a link to confirm the like. This doubles as a constraint: each user (email) can only like (each endpoint) once.

There can be multiple endpoints/domains (= *things to like*) that are identified by a unique string. This can e.g. be a
single one (`idea`), or a fixed or dynamic list (`project.1`, `project.2`, &hellip;) - the system actually doesn't
care. It will ask *your code* if a given endpoint is valid when listing/adding/confirming likes.  

The js widget talks to the server system via an API endpoint. It also acts as a proxy for confirming likes. For this to
work, we define a distinct URL fragment for the widget listens on that contains the 'confirm token' (same holds true for
the user token; see below).     

### Security & privacy 
* The email addresses itself are never stored - only a one way hash of them. Plus: the hash is unique for every endpoint
  and system.
* The API **does not** allow checking if a certain email address has already 'liked' the endpoint. The widget (client
  side), however, acquires a token and stores it in `localstorage` when requesting a new like. This token allows it to
  query information about the user's like (and therefore can provide a nicer UI).
* The user token gets appended to the 'confirm url' allowing the widget to read it and refresh the local version.  
 
#### Stored things on the server
* a table of hashed emails (see `Like` entity) in your database
  
#### Stored things at the client
* a user token (`localstorage`) 

## Setup

1. Require the bundle and register it.
   ```shell script
   composer require mvo/me-like
   ``` 

1. Render the widget to the desired endpoint in your twig template:

    ```twig
    {{ render(controller('mvo.me_like.widget', { 'endpoint': 'my-endpoint' })) }}
    ``` 

1. Add an endpoint handler (implementing `Mvo\MeLike\Endpoint\EndpointInterface`) that
   returns `VALID` for the endpoints you want to allow. You can also add random data
   context to the endpoint that will be available in the notification template.       
   
   ```php       
    class MyEndpoint implements EndpointValidatorInterface
    {
        public function handle(string $domain, ?int $id): ?bool
        {
            return 'my-endpoint' === $domain ? 
                EndpointValidatorInterface::VALID : 
                EndpointValidatorInterface::UNKOWN;
        }
   
       public function addContext(string $domain, ?int $id): ?array
       {
            return null;
       }
    }   
   ```
   
   In case your endpoint's name is in the form `<domain>.<id>` the individual parts will
   be passed to your handler.
   
   Note that a handler will only be asked to add context for an endpoint if `handle()`
   returns `VALID` for this endpoint. You do not need to re-check the validity inside
   `addContext()`.
   
1. If you're not using autoconfiguration, tag the handler it with `mvo_me_like.endpoint`. 
   
1. Make sure to load the js widget and minimal styles. The bundle ships with a compiled
   version (`like_widget.js`, `like_widget.css`) which automatically initializes the widgets
   and has some opinionated styles. Feel free to build your own version (see sources
   under `/layout`).

## Configuration
1. The systems sends emails via symfony mailer. Configure the following templates and 
   config keys to fit your needs:
    - Notification template: `@MvoMeLike\Email\confirm_like.html.twig` 
    - Mailer config:
      ```yaml
      mvo_me_like:
        notification:
            email_from: 'mail@example.com'
      ```

1. Adapt translations to your needs. The email subject can also be modified this way.
   If you need context sensitive parameters pass them in your endpoint handler under
   the key `email_subject`. 

1. You can also configure the token names (user token + confirm token) that are
   appended to the URL (e.g. in case you've got some anchors with the same names):
      ```yaml
      mvo_me_like:
        user_token: 'tok_u'
        confirm_token: 'tok_c'
      ```
   
   This will result in a 'confirm url' like so: 
   https://example.com/some/site#tok_u=<user-token>&tok_c=<confirm-token>
   
   All tokens have a length of 64 characters.