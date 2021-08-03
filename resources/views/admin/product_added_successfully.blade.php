<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <?php
        if(isset($_GET['pid']))
            $id = $_GET['pid']
    ?>
    <section class="product_added_successfully">
        درج انجام شد
        <br>
        شناسه: {{$id}}
    </section>

    <section class="add_feature">
        <input class="feature_name" type="text" placeholder="نام ویژگی" required>
        <input class="feature_value" type="text" placeholder="مقدار ویژگی" required>
        <button class="submit_feature">درج ویژگی</button>
    </section>

    <hr>
    <h6 style="text-align: right">ویژگی های مهم</h6>
    <section class="add_important_feature">
        <input class="important_feature_name" type="text" placeholder="نام ویژگی" required>
        <input class="important_feature_value" type="text" placeholder="مقدار ویژگی" required>
        <button class="submit_important_feature">درج ویژگی</button>
    </section>
    <hr>
    <h6 style="text-align: right">مشخصات فنی</h6>
    <section class="add_technical_specification">
        <input class="technical_specification_name" type="text" placeholder="نام ویژگی" required>
        <input class="technical_specification_value" type="text" placeholder="مقدار ویژگی" required>
        <button class="submit_technical_specification">درج ویژگی</button>
    </section>

    <hr>

<section class="add_image_to_gallery">
    <form action="https://api.khiyal.art/api/admin/add_image_to_ecommerce_product" method="post" enctype="multipart/form-data">
        <input type="hidden" name="token" value="test">
        <input type="hidden" name="product_id" value="{{$id}}">
        <input type="text" name="image_alt" placeholder="متن جایگزین تصویر" value="خیال راحت">
        <input type="file" name="product_image">
        <input class="submit_image" type="submit" value="درج">
    </form>
</section>

    <hr>

<section class="add_color_to_product">

    <select class="color">
        <option value=""></option>
    </select>
    <button class="submit_color">درج رنگ</button>
</section>
</body>
<style>
    *{
        direction: rtl;
    }
    .product_added_successfully{
        background: green;
        color: #fff;
        font-family: Dana;
        width: fit-content;
        padding: 15px;
        margin: 0 auto;
    }
    .add_feature{
        width: 500px;
        margin: 10px auto;
    }
    .add_feature input {
        font-family: Dana;
        display: inline-block;
        width: 45%;
        margin: 1%;
        text-align: right;
        padding: 5px;
        outline: none;
        box-sizing: border-box;
    }
    .add_feature button {
        display: block;
        font-family: Dana;
        margin: 20px auto;
    }
    .add_image_to_gallery{
        display: block;
        width: 500px;
        margin: 25px auto;
    }
    .add_image_to_gallery input{
        font-family: Dana;
        display: inline-block;
        width: 45%;
        margin: 1%;
        text-align: right;
        padding: 5px;
        outline: none;
        box-sizing: border-box;
    }
    .submit_image{
        width: 50px;
        padding: 5px;
        text-align: center;
        margin: 15px auto;
        display: block;
    }
    .add_color_to_product{
        display: block;
        width: 500px;
        margin: 15px auto;
    }
    .submit_color , .color{
        width: 48%;
        border-radius: 15px;
        outline: none;
        padding: 10px 15px;
        font-family: Dana;
    }
</style>
<script>
    let showColors
    showColors = new XMLHttpRequest()
    showColors.open('post' ,"https://api.khiyal.art/api/admin/show_all_colors")
    showColors.setRequestHeader('Accept', 'application/json')
    showColors.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
    showColors.send("token=test")
    showColors.onreadystatechange = function(){
        if(showColors.readyState === XMLHttpRequest.DONE){
            let response = JSON.parse(showColors.response)
            if(showColors.status === 200){
                if(response.message === 'all colors received successfully.'){
                    response.success.forEach(function(ind){
                        console.log('ind')
                        let colorOption = document.createElement("option")
                        let colorSelect = document.querySelector('.color')
                        colorOption.setAttribute('value' , ind.id)
                        colorOption.setAttribute('class' , "ic"+ind.id)
                        colorOption.innerHTML = ind.color
                        colorSelect.appendChild(colorOption)
                    })
                }
            }
        }
    }

    document.getElementsByClassName('submit_feature')[0].onclick = function(){
        if(document.getElementsByClassName('feature_name')[0].value === null) alert('نام ویژگی نباید خالی باشد')
        else if(document.getElementsByClassName('feature_value')[0].value === null) alert('مقدار ویژگی نباید خالی باشد')
        else{
            let add_feature
            add_feature = new XMLHttpRequest()
            add_feature.open('post' ,"https://api.khiyal.art/api/admin/add_feature_to_ecommerce_product")
            add_feature.setRequestHeader('Accept', 'application/json')
            add_feature.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
            add_feature.send("token=test&product_id={{$id}}&index="+document.getElementsByClassName('feature_name')[0].value+"&value="+document.getElementsByClassName('feature_value')[0].value)
            add_feature.onreadystatechange = function(){
                if(add_feature.readyState === XMLHttpRequest.DONE){
                    let response = JSON.parse(add_feature.response)
                    if(add_feature.status === 200){
                        if(response.message === 'feature added to product successfully.'){
                            document.getElementsByClassName('feature_name')[0].value = null
                            document.getElementsByClassName('feature_value')[0].value = null
                            alert('ویژگی به محصول با موفقیت اضافه شد.')
                        }
                    }
                }
            }
        }
    }

    document.getElementsByClassName('submit_important_feature')[0].onclick = function(){
        if(document.getElementsByClassName('important_feature_name')[0].value === null) alert('نام ویژگی نباید خالی باشد')
        else if(document.getElementsByClassName('important_feature_value')[0].value === null) alert('مقدار ویژگی نباید خالی باشد')
        else{
            let add_feature
            add_feature = new XMLHttpRequest()
            add_feature.open('post' ,"https://api.khiyal.art/api/admin/important_feature_add")
            add_feature.setRequestHeader('Accept', 'application/json')
            add_feature.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
            add_feature.send("token=test&product_id={{$id}}&index="+document.getElementsByClassName('important_feature_name')[0].value+"&value="+document.getElementsByClassName('important_feature_value')[0].value)
            add_feature.onreadystatechange = function(){
                if(add_feature.readyState === XMLHttpRequest.DONE){
                    let response = JSON.parse(add_feature.response)
                    if(add_feature.status === 200){
                        if(response.message === 'feature added to product successfully.'){
                            document.getElementsByClassName('important_feature_name')[0].value = null
                            document.getElementsByClassName('important_feature_value')[0].value = null
                            alert('ویژگی به محصول با موفقیت اضافه شد.')
                        }
                    }
                }
            }
        }
    }

    getElementsByClassName('submit_technical_specification')[0].onclick = function(){
        if(document.getElementsByClassName('technical_specification_name')[0].value === null) alert('نام ویژگی نباید خالی باشد')
        else if(document.getElementsByClassName('technical_specification_value')[0].value === null) alert('مقدار ویژگی نباید خالی باشد')
        else{
            let add_feature
            add_feature = new XMLHttpRequest()
            add_feature.open('post' ,"https://api.khiyal.art/api/admin/delete_product")
            add_feature.setRequestHeader('Accept', 'application/json')
            add_feature.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
            add_feature.send("token=test&product_id={{$id}}&index="+document.getElementsByClassName('technical_specification_name')[0].value+"&value="+document.getElementsByClassName('technical_specification_value')[0].value)
            add_feature.onreadystatechange = function(){
                if(add_feature.readyState === XMLHttpRequest.DONE){
                    let response = JSON.parse(add_feature.response)
                    if(add_feature.status === 200){
                        if(response.message === 'feature added to product successfully.'){
                            document.getElementsByClassName('technical_specification_name')[0].value = null
                            document.getElementsByClassName('technical_specification_value')[0].value = null
                            alert('مشخصه فنی به محصول با موفقیت اضافه شد.')
                        }
                    }
                }
            }
        }
    }


    document.getElementsByClassName('submit_color')[0].onclick = function(){
        if(document.getElementsByClassName('color')[0].value === null) alert('رنگ نباید خالی باشد')
        else{
            let add_feature
            add_feature = new XMLHttpRequest()
            add_feature.open('post' ,"https://api.khiyal.art/api/admin/add_color_to_product")
            add_feature.setRequestHeader('Accept', 'application/json')
            add_feature.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
            add_feature.send("token=test&product_id={{$id}}&color_id="+document.getElementsByClassName('color')[0].value)
            add_feature.onreadystatechange = function(){
                if(add_feature.readyState === XMLHttpRequest.DONE){
                    let response = JSON.parse(add_feature.response)
                    if(add_feature.status === 200){
                        if(response.message === 'color added to product successfully.'){
                            alert('رنگ به محصول با موفقیت اضافه شد.')
                            document.querySelector("."+document.getElementsByClassName('color')[0].value).style.display = 'none'
                        }
                    }else{
                        alert('این رنگ برای این محصول اضافه شده است');
                    }
                }
            }
        }
    }
</script>
</html>
