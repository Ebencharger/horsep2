<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        .body {
            width: 100%;
            height: fit-content;
            background-color: white;
            box-shadow: 0px 0px 2px;
            padding-top: 1%;
        }

        .container {
            background-color: #16b7e0;
            width: 100%;
            height: fit-content;
            color: white;
            padding: 2%;
        }

        .container p {
            white-space: pre-line;
        }

        .img {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .linkHolder {
            width: 100%;
            height: 100px;
            padding: 2%;
            background-color: rgb(255, 255, 255);
        }

        .linkHolder a {
            color: black;
        }
    </style>
</head>

<body>
    <div class="body">
        <div class="img">
            <img
                width="200"
                src="{{ Storage::url('images/logo.svg') }}"
                alt="" />
        </div>
        <div class="container">
            <p> {!! $data['message'] !!}</p>
        </div>
        <div style="padding: 5%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 14px;">
            <p>
                Best regards,
            </p>
            <p>Operational Support,</p>
            <p>Horsep2.</p>
        </div>
    </div>
</body>

</html>