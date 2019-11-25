<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Code Coverage Reports</title>
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
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="mt-5 text-center">Code Coverage</h3>
                <div class="mt-5">
                    <div class="text-center">
                        <span class="badge badge-success"><?php echo $positive; ?> Covered Lines</span>
                        <span class="badge badge-danger"><?php echo $negative; ?> Uncovered Lines</span>
                        <span class="badge badge-primary"><?php echo $percentage; ?>&percnt;</span>
                    </div>

                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percentage; ?>%</div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo 100 - $percentage; ?>%" aria-valuenow="<?php echo 100 - $percentage; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo 100 - $percentage; ?>%</div>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <a href="annotations.html" class="btn btn-link">Annotations Report</a>
                </div>

                <h5 class="mt-5">All Files</h5>
                <ul class="list-unstyled">
                    <?php foreach ($files as $file): ?>
                        <li>
                            <a target="_blank" href="<?php echo ltrim(str_replace('.php', '.html', $file), '/'); ?>"><?php echo $file ?></a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
