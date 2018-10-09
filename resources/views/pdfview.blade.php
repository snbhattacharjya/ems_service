<!DOCTYPE html>

<html>

<head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <style>
        .page-break {
            page-break-after: always;
        }
        </style>


</head>

<body>
              
    <div class="container mt-3">
    
    <div class="d-flex justify-content-center mb-3">Government of West Bengal</div>
    <div class="d-flex justify-content-center mb-3">Office of the District Election Officer & District Magistrate,Hooghly</div>
     <div class="d-flex justify-content-center mb-3">District Polling Personnel Cell,Hooghly</div>
    <div class="d-flex justify-content-center mb-3"> Email: ppcell.hooghly@gmail.com </div>

  
    
    </div>
 <div style="border-bottom: 1px solid black"> ELECTION URGENT</div>

To,
{{$val->name}}
{{$val->address}}
{{$val->pin}}

Sub: Preparation of PP database in connection with ensuing General Parliament Election-2019

Ref:

In connection with the General Parliament Election-2019,you are 

 @foreach($userinfo as $val)  
<h4>Office Name :{{$val->name.'('.$val->userId.')' }} </h4>
  <p><strong>Please check below details<strong></p>
   <p>Your Office User Id:{{$val->userId}} </p> 
 <p>Password:{{$val->userPassword}} </p> 
<div class="page-break"></div>

 @endforeach 



</body>

</html>