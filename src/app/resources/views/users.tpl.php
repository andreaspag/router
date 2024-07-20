<!DOCTYPE html>
<html>
    <head>
        <title>Sample Code</title>
    </head>
    <body>
        <h1>A list of users</h1>
        <div>
            <ul>
            <?php foreach($data as $user) { 
                echo '<li>'. $user .'</li>';
            } ?>
            <ul>
        </div>
        <hr />
        <div>Click on the following links</div>
        [<a href="/">Home</a>] [<a href="/v1/users">Users</a>] [<a href="/user/register">Register</a>]

    </body>
</html>