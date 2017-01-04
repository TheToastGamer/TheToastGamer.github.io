<?php

    function versions()
    {
        $info = preg_split( "/[\/ ]/", $_SERVER['SERVER_SOFTWARE'] );
        foreach ( $info  as $key => $value ) {
            switch( strtoupper( $value ) )
            {
                case 'APACHE':
                    $data['apache'] = explode(".", $info[$key+1]);
                    break;
                case 'PHP':
                    $data['php'] = explode(".", $info[$key+1]);
                    break;
                case 'MOD_SSL':
                    $data['modssl'] = explode(".", $info[$key+1]);
                    break;
                case 'OPENSSL':
                    $data['openssl'] = explode(".", $info[$key+1]);
                    break;
            }
        }
        return $data;
    }

    function apache_module_exists( $module )
    {
        return in_array($module, apache_get_modules());
    }

    function check_array( $data ){
        foreach ( $data as &$value ) {
            if ( $value == false ){
                return false;
            }
        }
        return true;
    }

    function htaccess_check( )
    {
        $data = array(
            "test.html"     => "<html><head><title></title></head><body></body></html>",
            "test2.html"    => "<html><head><title></title></head><body></body></html>",
            ".htaccess"     => "redirect 301 " . $_SERVER['REQUEST_URI'] . "test.html " . $_SERVER['REQUEST_URI'] . "test2.html"
        );

        foreach ( $data  as $key => $value ){
            $file = fopen( $key, "w" );
            fwrite( $file , $value );
            fclose( $file );
        }

        $http = curl_init( $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "test.html" );
        curl_setopt($http, CURLOPT_RETURNTRANSFER, true);
        curl_exec($http);
        $code = curl_getinfo($http, CURLINFO_HTTP_CODE);

        foreach ( $data  as $key => $value ){
            unlink($key);
        }

        if ($code == 301){
            return true;
        }else{
            return false;
        }
    }

    $app_versions = versions();

    $requirements = array(
        "PHP_VERSION"       => isset($app_versions['php']) && $app_versions['php'][0] >= 5 &&  $app_versions['php'][1] >= 0,
        "APACHE_VERSION"    => isset($app_versions['apache']) && $app_versions['apache'][0] >= 2 &&  $app_versions['apache'][1] >= 0
    );

    $php_extensions = array(
        "JSON"               => array( "Json", extension_loaded ('json') )
    );

    $apache_extensions = array(
        "PHP5"              => array( "PHP 5", apache_module_exists('mod_php5') )
    );

    foreach ($php_extensions as $key => $value) {
        $requirements[$key] = $value[1];
    }

    foreach ($apache_extensions as $key => $value) {
        $requirements[$key] = $value[1];
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Hex Load Tester</title>
    <meta name="generator" content="Bootply" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <!-- CSS -->
    <style type="text/css">

        @import url(http://fonts.googleapis.com/css?family=Antic+Slab);

        html,body {
            height:100%;
        }

        h1 {
            font-family: 'Antic Slab', serif;
            font-size:80px;
            color:#fff;
        }

        .lead {
            color:#fff;
        }
        .lead small {
            font-size: 12px;
            line-height: 12px;
        }

        .container-full {

            margin: 0 auto;
            width: 100%;
            min-height:100%;
            color:#fff;
            overflow:hidden;

            background: rgb(0,207,234); /* Old browsers */
            background: -moz-linear-gradient(top,  rgba(0,207,234,1) 0%, rgba(0,177,216,1) 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(0,207,234,1)), color-stop(100%,rgba(0,177,216,1))); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top,  rgba(0,207,234,1) 0%,rgba(0,177,216,1) 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top,  rgba(0,207,234,1) 0%,rgba(0,177,216,1) 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top,  rgba(0,207,234,1) 0%,rgba(0,177,216,1) 100%); /* IE10+ */
            background: linear-gradient(to bottom,  rgba(0,207,234,1) 0%,rgba(0,177,216,1) 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00cfea', endColorstr='#00b1d8',GradientType=0 ); /* IE6-9 */

        }

        .container-full h1 {
            text-shadow: 0 1px 0 #ccc,
            0 2px 0 #c9c9c9,
            0 3px 0 #bbb,
            0 4px 0 #b9b9b9,
            0 5px 0 #aaa,
            0 6px 1px rgba(0,0,0,.1),
            0 0 5px rgba(0,0,0,.1),
            0 1px 3px rgba(0,0,0,.3),
            0 3px 5px rgba(0,0,0,.2),
            0 5px 10px rgba(0,0,0,.25),
            0 10px 10px rgba(0,0,0,.2),
            0 20px 20px rgba(0,0,0,.15);
        }

        .container-full a {
            color:#5171d1;
            text-decoration:none;
        }
        .container-full a:hover {
            color:#5171d1;
            text-decoration:none;
        }

        .v-center {
            position: absolute;
            top: 50%;
            margin-top:-150px;
        }

        td {
            padding: 8px 0px;
            text-align: left;
        }

        td:last-child {
            padding: 8px 0px;
            text-align: right
        }

        code.ok {
            background: #d9ffce;
            color: #44aa14;
        }

    </style>
</head>
<body  >
<div class="container-full">
    <div class="row">
        <div class="col-lg-12 text-center v-center">
            <!-- glyphicon-remove-sign -->
            <?php
            if ( check_array( $requirements ) ){
                echo (
                    "<h1><span class='fa fa-heart'></span> It'll run!</h1><br>".
                    "<p class='lead'>It looks like Hex Load will work on your web server! <i class='fa fa-smile-o'></i></p>".

                    "<small>Thank you for the support!</small><br><br>".
                    "<a class='btn btn-default' href='https://scriptfodder.com/scripts/view/1361'><i class='fa fa-heart'></i> Click here to buy!</a>"
                );
            }else{
                echo (
                    "<h1><span class='fa fa-bomb'></span> Aw Snap!</h1><br>".
                    "<p class='lead'>It looks like Hex Load won't run on your server! <span class='fa fa-frown-o'></span> <br>".
                    "Contact your server host and ask them install update the requirements below.<br>".

                    "<small>You can buy the script but there's no guarantee it will work.".
                    " Buy at you own risk, there will be no support if server requirements are not met.</small><br><br>".
                    "<a class='btn btn-default' href='https://scriptfodder.com/scripts/view/1361'><i class='fa fa-heart'></i> Click here to buy!</a>"
                );
            }
            ?>

        </div>
    </div>
    <br><br><br><br><br>
</div>

<div class="container">
    <hr>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><h3><span class="fa fa-code"></span> PHP Requirements</h3></div>
                <div class="panel-body">
                    <table width="100%">
                        <tr>
                            <col width="50%">
                            <col width="50%">
                            <td>Your Version</td>
                            <?php
                                if (  $requirements["PHP_VERSION"] == true ){
                                    echo ("<td><code class='ok' ><i class='fa fa-thumbs-o-up'></i> " . implode( ".", $app_versions['php'] ) . "</code></td>");
                                }else{
                                    echo ("<td><code><i class='fa fa-exclamation-triangle'></i> " . implode( ".", $app_versions['php'] ) . "</code></td>");
                                }
                            ?>
                        </tr>
                        <tr>
                            <td>Required Version</td>
                            <td><code class="ok" > >= 5.x.x</code></td>
                        </tr>
                    </table>
                    <hr>
                    <table width="100%">
                        <tr>
                            <col width="50%">
                            <col width="50%">
                        </tr>
                        <?php
                        foreach ( $php_extensions as $key => $value ) {
                            echo ( "<tr>");
                            echo ( "<td>$value[0]</td>" );
                            if ( $value[1] == true ){
                                echo ( "<td><code class='ok' ><i class='fa fa-thumbs-o-up'></i> Installed!</code></td>" );
                            }else{
                                echo ( "<td><code><i class='fa fa-exclamation-triangle'></i> Not Installed!</code></td>" );
                            }
                            echo ( "</tr>");
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><h3><span class="fa fa-code"></span> Apache Requirements</h3></div>
                <div class="panel-body">
                    <table width="100%">
                        <tr>
                            <col width="50%">
                            <col width="50%">
                            <td>Your Version</td>
                            <?php
                                if( isset($app_versions['apache']) ){
                                    if (  $requirements["APACHE_VERSION"] == true ){
                                        echo ("<td><code class='ok' ><i class='fa fa-thumbs-o-up'></i> " . implode( ".", $app_versions['apache'] ) . "</code></td>");
                                    }else{
                                        echo ("<td><code><i class='fa fa-exclamation-triangle'></i> " . implode( ".", $app_versions['apache'] ) . "</code></td>");
                                    }
                                }else{
                                    echo ("<td><code><i class='fa fa-exclamation-triangle'></i> Apache not found!</code></td>");
                                }
                            ?>
                        </tr>
                        <tr>
                            <td>Required Version</td>
                            <td><code class="ok" > >= 2.x.x</code></td>
                        </tr>
                    </table>
                        <?php
                            if( isset($app_versions['apache']) ){
                                echo ( "<hr>");
                                echo ( "<table width='100%'>");
                                echo ( "<tr><col width='50%'><col width='50%'></tr>");
                                foreach ( $apache_extensions as $key => $value ) {
                                    echo ( "<tr>");
                                    echo ( "<td>$value[0]</td>" );
                                    if( $key == 'HTACCESS' ){
                                        if(! extension_loaded('curl') ){
                                            echo ( "<td><code><i class='fa fa-exclamation-triangle'></i> CUrl required!</code></td>" );
                                        }else{
                                            if ( $value[1] == true ){
                                                echo ( "<td><code class='ok' ><i class='fa fa-thumbs-o-up'></i> Installed!</code></td>" );
                                            }else{
                                                echo ( "<td><code><i class='fa fa-exclamation-triangle'></i> Not Installed!</code></td>" );
                                            }
                                        }
                                    }elseif ( $value[1] == true ){
                                        echo ( "<td><code class='ok' ><i class='fa fa-thumbs-o-up'></i> Installed!</code></td>" );
                                    }else{
                                        echo ( "<td><code><i class='fa fa-exclamation-triangle'></i> Not Installed!</code></td>" );
                                    }
                                    echo ( "</tr>");
                                }
                                echo ( "</table>");
                            }
                        ?>
                </div>
            </div>
        </div>

    </div>

    <hr>

    <div class="row">
        <div class="col-lg-12">
            <br><br>
        </div>
    </div>
</div>

</body>
</html>