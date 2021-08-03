<?php
    if(isset($_GET['cid']))
        $id = $_GET['cid']
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>دوره</title>
    <style>
        .success{
            width: 200px;
            padding: 15px;
            border-radius: 15px;
            background-color: #3b8070;
            color: #ffffff;
            margin: 15px auto;
            text-align: right;
            font-family: dana;
            direction: rtl;
        }
        .add_section_to_course{
            display: block;
            width: 600px;
            margin: 30px auto;
            background: #ccc;
            padding: 15px;
            border-radius: 15px;
        }
        .section_index{
            display: block;
            float: right;
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border-radius: 15px;
            border: 1px solid #aaa;
        }
        .add_section_button{
            width: 30%;
            padding: 6px;
            font-family: dana;
            margin: 15px auto;
            display: block;
            border-radius: 35px;
            border: 1px solid #aaa;
        }
        .section_description{
            width: 100%;
            margin: 15px 0;
            border-radius: 15px;
            height: 150px;
        }
    </style>
</head>
<body>

    <section class="success">

        دوره با موفقیت درج شد.
        <br>
        {{ 'شناسه دوره:' . $id }}
    </section>

    <section class="add_section_to_course">
        <input type="text" class="section_index" placeholder="نام بخش">
        <textarea class="section_description"></textarea>
        <button class="add_section_button"> افزودن قسمت</button>
    </section>
    <script>
        document.querySelector('.add_section_button').onclick = function(){
            if(document.querySelector('.section_index').value === ''){
                alert('نام بخش نمیتواند خالی باشد')
            }else if(document.querySelector('.section_description').value === ''){
                alert('توضیحات بخش نمیتواند خالی باشد')
            }else{

                let add_feature
                add_feature = new XMLHttpRequest()
                add_feature.open('post' ,"https://api.khiyal.art/api/admin/add_section/{{$id}}")
                add_feature.setRequestHeader('Accept', 'application/json')
                add_feature.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
                add_feature.send("token=test&title="+document.querySelector('.section_index').value+"&description="+document.querySelector('.section_description').value)
                add_feature.onreadystatechange = function(){
                    if(add_feature.readyState === XMLHttpRequest.DONE){
                        let response = JSON.parse(add_feature.response)
                        if(add_feature.status === 200){
                            if(response.message === "section added to course successfully."){
                                alert('بخش  به دوره با موفقیت اضافه شد.')
                                document.querySelector('.section_index').value = ''
                                document.querySelector('.section_description').value = ''
                            }
                        }else{
                            alert('افزودن بخش موفقیت آمیز تبود');

                        }
                    }
                }
            }
        }
    </script>

</body>
</html>
