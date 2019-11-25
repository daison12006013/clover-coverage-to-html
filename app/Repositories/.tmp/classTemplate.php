<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $title ?></title>
    <style>
        .positive {
            background-color: green;
            color: white;
            font-style: inherit;
        }
        .negative {
            background-color: red;
            color: white;
            font-style: inherit;
        }
        .line-tab {
            tab-size: 4;
        }
        .code-block {
            background: #434343;
            border: 1px solid #111;
            border-radius: 3px;
        }
        .code-block pre {
            padding: 30px;
            color: #f1f1f1;
            font-size: inherit;
            max-height: 80vh;
            overflow-y: auto;
        }
        .div-code {
            font-size: 100%;
            counter-increment: line;
            display: inline-block;
            width: 100%;
        }
        .div-code:hover {
            background-color: #111;
        }
        .div-code::before {
            content: counter(line) " ";
            display: inline-block;
            padding-left: auto;
            margin-left: auto;
            text-align: right;
        }
        .no-border {
            border: 0px;
        }
        th {
            text-transform: inherit !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/lux/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
    <script>
    $('document').ready(function(){
        $('[data-toggle=tooltip]').tooltip();
    });
    </script>

    <div class="container">
        <div class="row mt-5 mb-5">
            <h4 class="">Statistics</h4>
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <td>Overall</td>
                            <td><span class="badge badge-success"><?php echo $calculations['positive']; ?></span></td>
                            <td><span class="badge badge-danger"><?php echo $calculations['negative']; ?></span></td>
                            <td><span class="badge badge-primary"><?php echo $calculations['percentage']; ?>&percnt;</span></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="p-0">
                                <table class="no-border table table-bordered">
                                    <tr>
                                        <th colspan="4" class="text-center">Methods</th>
                                    </tr>
                                    <?php foreach ($calculations['methods'] as $name => $value): ?>
                                    <tr>
                                        <?php if (count($value['annotations'])): ?>
                                            <td data-toggle="tooltip" data-placement="left" data-html="true" title="<b><u>Annotations</u></b> <ul class='list-unstyled'><?php
                                                    echo implode('', array_map(function ($val) {
                                                        return sprintf("<li>%s</li>", trim($val));
                                                    }, $value['annotations']));
                                                ?></ul>">
                                        <?php else: ?>
                                            <td data-toggle="tooltip" data-placement="left" data-html="true" title="No annotations found!">
                                        <?php endif ?>

                                            <?php echo $name; ?>
                                        </td>
                                        <td><span class="badge badge-success"><?php echo $value['positive']; ?></span></td>
                                        <td><span class="badge badge-danger"><?php echo $value['negative']; ?></span></td>
                                        <td><span class="badge badge-primary"><?php echo $value['percentage']; ?>&percnt;</span></td>
                                    </tr>
                                    <?php endforeach ?>
                                </table>
                            </td>
                        </tr>
                        <?php if ($calculations['annotations']): ?>
                        <tr>
                            <td colspan="4" class="p-0">
                                <table class="no-border table table-bordered">
                                    <tr>
                                        <th colspan="4" class="text-center">
                                            Annotations
                                        </th>
                                    </tr>
                                    <?php foreach ($calculations['annotations'] as $flag => $values): ?>
                                        <tr>
                                            <th colspan="4" class="text-center">
                                                &commat;<?php echo $flag; ?>
                                            </th>
                                        </tr>
                                        <?php foreach ($values as $name => $value): ?>
                                        <tr>
                                            <td data-toggle="tooltip" data-placement="bottom" data-html="true" title="<b><u>Methods</u></b> <ul class='list-unstyled'><?php
                                                    echo implode('', array_map(function ($val) {
                                                        return sprintf("<li>%s</li>", trim($val));
                                                    }, $value['methods']));
                                                ?></ul>">

                                                <?php echo $name; ?>
                                            </td>
                                            <td><span class="badge badge-success"><?php echo $value['positive']; ?></span></td>
                                            <td><span class="badge badge-danger"><?php echo $value['negative']; ?></span></td>
                                            <td><span class="badge badge-primary"><?php echo $value['percentage']; ?>&percnt;</span></td>
                                        </tr>
                                        <?php endforeach ?>
                                    <?php endforeach ?>
                                </table>
                            </td>
                        </tr>
                        <?php endif ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <h4>Source Code</h4>
            <div class="col-md-12 mx-auto">
                <div class="mb-3">
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $calculations['percentage']; ?>%" aria-valuenow="<?php echo $calculations['percentage']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $calculations['percentage']; ?>%</div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo 100 - $calculations['percentage']; ?>%" aria-valuenow="<?php echo 100 - $calculations['percentage']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo 100 - $calculations['percentage']; ?>%</div>
                    </div>
                </div>

                <div class="code-block">
                    <?php
                        // transform the content lines to have this kind of template per line
                        $lineTemplate = '<div class="div-code"><span class="line-tab"></span>%s</div>';

                        $content = implode("\n", array_map(function ($val) use ($lineTemplate) {
                            return sprintf($lineTemplate, $val);
                        }, explode("\n", $content)));
                    ?>
                    <pre><?php echo $content; ?></pre>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
