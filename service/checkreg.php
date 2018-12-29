<?php
  class Match {
    public function checkMobile ($mobile) {
       $reg='/^1[34578]\d{9}$/ims';
       if(preg_match($reg,$mobile)){
          return '';
       }else{
           return '手机号格式有误';
       }
    }
  }
