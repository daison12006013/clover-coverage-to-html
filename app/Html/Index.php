<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .file-coverage-path {
            margin-top: 1rem;
        }
        .badge-search:hover {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script>
        $(function () {
            $(".badge-search").on("click", function (e) {
                $(".file-coverage-path").addClass("d-none");
                $(".badge-search").addClass("badge-dark");
                $(this).removeClass("badge-dark");
                $(this).addClass("badge-primary");

                var numberOfFiles = 0;
                var coveredLines = 0;
                var uncoveredLines = 0;

                $.each($(this).data("values"), function (key, searchVal) {
                    $.each($(".file-coverage-path a"), function () {
                        if ($(this).text().search(new RegExp(searchVal, "g")) != -1) {
                            $(this).closest(".file-coverage-path").removeClass("d-none");

                            numberOfFiles += 1;
                            coveredLines += $(this).closest(".file-coverage-path").find(".badge-success").data("line");
                            uncoveredLines += $(this).closest(".file-coverage-path").find(".badge-danger").data("line");
                        }
                    });
                });

                $(".number-of-files").text(numberOfFiles);
                $(".covered-lines").text(coveredLines);
                $(".uncovered-lines").text(uncoveredLines);

                var green = (coveredLines / (coveredLines + uncoveredLines) * 100).toFixed(2);
                var red = (uncoveredLines / (coveredLines + uncoveredLines) * 100).toFixed(2);

                $(".progress").html(
                    '<div class="progress-bar bg-success"'+
                    ' role="progressbar" style="width: '+green+'%" '+
                    'aria-valuenow="'+green+'" aria-valuemin="0" '+
                    'aria-valuemax="100">'+green+'%</div><div '+
                    'class="progress-bar bg-danger" role="progressbar" '+
                    'style="width: '+red+'%" aria-valuenow="'+red+'" '+
                    'aria-valuemin="0" aria-valuemax="100">'+red+'%</div>'
                );
            });
        });
    </script>
    <div class="container">
        <div class="row mt-5 mb-3">
            <div class="col-12 text-center">
                <h4><?php echo $title; ?></h4>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-4">
                Number of Files: <strong class="number-of-files"><?php echo $numberOfFiles; ?></strong>
            </div>
            <div class="col-4">
                Covered Lines: <strong class="covered-lines"><?php echo $greenLines; ?></strong>
            </div>
            <div class="col-4">
                Uncovered Lines: <strong class="uncovered-lines"><?php echo $redLines; ?></strong>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $greenPercent; ?>%" aria-valuenow="<?php echo $greenPercent; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $greenPercent; ?>%</div>
                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $redPercent; ?>%" aria-valuenow="<?php echo $redPercent; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $redPercent; ?>%</div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-8 mx-auto text-center">
                <span data-values='[""]' class="badge-search badge badge-pill badge-primary">
                    All
                </span>

                <?php foreach ($badges as $name => $values): ?>
                <span data-values='<?php echo json_encode(is_array($values) ? $values : [$values]); ?>' class="badge-search badge badge-pill badge-dark">
                    <?php echo $name; ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12 mx-auto">
                <ul class="list-unstyled">
                    <?php echo $contents; ?>
                </ul>
            </div>
        </div>

        <footer class="mb-5 text-center footer">
            Developed and Created By Daison Cariño
        </footer>
    </div>
</body>
</html>
