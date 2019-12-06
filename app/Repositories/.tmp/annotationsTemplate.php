<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Annotations Report</title>
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
    </style>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/lux/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
    <script>
    $('document').ready(function(){
        $('[data-toggle=modal]').on('show.bs.modal');
        $('[data-toggle=tooltip]').tooltip();
    });
    </script>
    <div class="container">
        <div class="mt-5 mb-5 row">
            <div class="col-12">
                <h4 class="text-center">Annotations Report</h4>
                <?php foreach ($annotations as $flag => $annotation): ?>
                    <h3><?php echo $flag ?></h3>

                    <?php foreach ($annotation as $annoName => $info): ?>
                        <?php
                            $rand = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
                        ?>

                        <h5>
                            <?php echo $annoName ?>
                            <span class="badge badge-success"><?php echo $info['positive']; ?> Covered Lines</span>
                            <span class="badge badge-danger"><?php echo $info['negative']; ?> Uncovered Lines</span>
                            <span class="badge badge-primary"><?php echo $info['percentage']; ?>&percnt;</span>
                            <a class="btn btn-link" href="#" data-toggle="modal" data-target="#<?php echo $rand ?>Modal">
                                Show Files
                            </a>
                        </h5>

                        <div class="modal fade" id="<?php echo $rand ?>Modal" tabindex="-1" role="dialog" aria-labelledby="<?php echo $rand ?>ModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><?php echo $annoName ?></h5>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-unstyled">
                                            <?php foreach ($info['files'] as $fileName => $value): ?>
                                                <li>
                                                    <div class="mt-1 mb-1">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <a target="_blank" href="<?php echo ltrim(str_replace('.php', '.html', $fileName), '/'); ?>"
                                                                    data-toggle="tooltip" data-placement="top" title="<?php echo $fileName ?>"
                                                                >
                                                                    <?php
                                                                        echo strlen($fileName) >= 50 ? '...'.substr($fileName, -50) : $fileName;
                                                                    ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row">
                                                                    <div class="col-4">
                                                                        <span class="badge badge-success"><?php echo $value['positive']; ?></span>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <span class="badge badge-danger"><?php echo $value['negative']; ?></span>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <span class="badge badge-primary"><?php echo $value['percentage']; ?>&percnt;</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</body>
</html>
