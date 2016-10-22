<?php
/**
 * @var Nip\View $this
 * @var ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseResponse $response
 */
$response = $this->get('response');
$model = $response->getModel();
$messageType = $response->getMessageType();
$title = translator()->translate('payment-gateways.messages.confirm.'.$messageType.'.title');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?php echo $title; ?></title>
    <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Open+Sans:400,300'>
    <link rel="stylesheet" type='text/css' href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type='text/css' href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            padding: 0;
            background-color: #fffffe;
            color: #1a1a1a;
            text-align: center;
        }

        .header {
            margin-top: 100px;
            padding-top: 10px;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1 style="font-size: 36px; color: <?php echo $response->getIconColor() ?>">
        <i class="<?php echo $response->getIconClass() ?>" aria-hidden="true"></i>
        <?php echo $title; ?>
    </h1>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h4>
                <?php echo $model->getPurchaseName(); ?>
            </h4>
            <hr/>

            <p>
                <?php
                echo $this->Messages()->$messageType($model->getManager()->getMessage('confirm.'.$model->status));
                ?>
            </p>

            <?php if (!$response->isSuccessful()) { ?>
                <p>
                    <?php
                    /** @noinspection PhpStaticAsDynamicMethodCallInspection */
                    echo $this->Messages()->error(
                        '<strong>'
                        .translator()->translate('payment-gateways.messages.confirm.error.message')
                        .'</strong>:<br />'
                        .$response->getMessage()
                    );
                    ?>
                </p>
            <?php } ?>

            <?php if ($response->isRedirect() || $response->hasButton()) { ?>
                <?php $src = $response->isRedirect() ? $response->getRedirectUrl() : $response->getButtonHref() ?>
                <?php $label = $response->hasButton() ? $response->getButtonLabel() : 'Return to merchant'; ?>

                <form action="<?php echo $src ?>" name="form-confirm" id="form-confirm" method="GET">
                    <button class="btn btn-success btn-md">
                        <i class="fa fa-mouse-pointer" aria-hidden="true"></i>
                        <?php echo $response->getButtonLabel(); ?>
                    </button>
                </form>

            <?php if ($response->isRedirect()) { ?>
                <script>
                    var timer = setTimeout(function () {
                        document.forms[0].submit();
                    }, 3000);
                </script>
            <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>
</body>
</html>