<!DOCTYPE html>
<html>
    <head>
        <title>Sample Code</title>
    </head>
    <body>
        <h1>This is a demo of the Router/Route/Dispatcher set of classes</h1>
        <div>
            <p>
                Please examine the following classes carefully.
                <ul>
                    <li>src/app/Router.php</li>
                    <li>src/app/Route.php</li>
                    <li>src/app/Dispatcher.php</li>
                </ul>
            </p>
        </div>

        <h2>The Router.php class</h2>
        <div>
            <p>
                Allows you to define routes and the HTTP methods required to access them. <strong>See /src/public/index.php for details</strong>
            </p>
        </div>

        <h2>The Dispatcher.php class</h2>
        <div>
            <p>
                Dispatches callables (functions, class methods, or invokable classes) which have been linked to routes using the Router class.
            </p>
        </div>

        <h2>The Route.php class</h2>
        <div>
            <p>
                Helper class representing a matched route
            </p>
        </div>

        <h2>Limitations</h2>
        <div>
            <p>
               At this point you cannot define routes with segments having the same name.
               For example for the following defined route will cause an error /hello/{hello}
            <p>
        </div>



        <hr />

        <div>Click on the following links</div>

        [<a href="/">Home</a>] [<a href="/v1/users">Users</a>] [<a href="/user/register">Register</a>]

    </body>
</html>