<?php
require_once ('../config.php');
require_once ('./include/index.php');
require_once ('./include/function/captcha.php');
$key = md5(randstr(32));
if(!isset($_COOKIE['_xsrf'])){
    setcookie('_xsrf', $key, time()+3600);
    $xsrf = 'chushihua';
}else{
    $xsrf = $_COOKIE['_xsrf'];
}
$flag = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $captcha_x = $_POST['captcha_x'];
    $captcha_y = $_POST['captcha_y'];

    $ans_pos_x_1 = $_SESSION['captcha']['ans_pos_x_1'];
    $ans_width_x_1 = $_SESSION['captcha']['ans_width_x_1'];
    $ans_pos_y_1 = $_SESSION['captcha']['ans_pos_y_1'];
    $ans_height_y_1 = $_SESSION['captcha']['ans_height_y_1'];
    unset($_SESSION['captcha']);
    if($ans_pos_x_1 > $captcha_x || $captcha_x > ($ans_pos_x_1 + $ans_width_x_1)){
        $flag = 'no';
    }elseif($ans_pos_y_1 > $captcha_y || $captcha_y > ($ans_pos_y_1 + $ans_height_y_1)){
        $flag = 'no';
    }
    $flag = 'yes';
}
if($flag == 'yes'){
	echo <<<EOT
		<div class="alert alert-success alert-dismissable">
                操作成功。
            </div>
EOT;
    die('<meta http-equiv="Refresh" content="0;url=/login"/>');
}
elseif($flag == 'no'){
	echo <<<EOT
		<div class="alert alert-danger alert-dismissable">
                操作失败。
            </div>
EOT;
}
$dir = "./captcha/ans/";
$files = my_dir("./captcha/ans");
$filesNum = count($files);
$randNum = mt_rand(0, $filesNum - 1);
$captchaId = str_replace('ans','',(explode(".",$files[$randNum])));
$captchaId = $captchaId[0];
$captchajpg = search_file("./captcha/jpgs",$captchaId);
$answer = @file_read($dir.($files[$randNum]));
// 验证码存入session
$_SESSION['captcha'] = $answer;
?>
        <div class="row">
            <form action="/pass/reset.php" method="post" class="col-lg-6 col-lg-offset-3">
                <div class="form-group">
                    <label>邮件地址：</label>
                    <input type="email" class="form-control" name="mail" placeholder="" required>
                </div>
                 <div>
                    <label><?php echo $answer['vtt_ques']?></label><br/>
                    <canvas id="vtt_captcha" width="680" height="460" rel="<?php echo $captchaId?>"></canvas>
                    <input type="hidden" id="captcha_x" name="captcha_x" value="">
                    <input type="hidden" id="captcha_y" name="captcha_y" value="">
                </div>
                <input type="hidden" name="_xsrf" value="<?php echo $xsrf?>">
                <button class="btn btn-primary pull-right" type="submit">注册</button>
            </form>
            </div>
        <br/>
<?php
echo <<<EOT
<script>
    window.onload = function () {
        var c = document.getElementById("vtt_captcha");
        var ctx = c.getContext("2d");
        var img = new Image();
        img.onload = function () {
            ctx.drawImage(img, 0, 0);
        };
        img.src = '/captcha/jpgs/$captchajpg';

        c.onmousedown = function (event) {
            var rect = this.getBoundingClientRect();
            var x = (event.x - rect.left) * (this.width / rect.width);
            var y = (event.y - rect.top) * (this.height / rect.height);


            ctx = c.getContext("2d");
            ctx.beginPath();
            ctx.arc(x,y,10,0,360,false);
            ctx.fillStyle="red";
            ctx.fill();
            ctx.closePath();

            document.getElementById('captcha_x').value = x;
            document.getElementById('captcha_y').value = y;
        }
    };
</script>
EOT;
?>
    <footer class="footer">
        <p>&copy; 2016 Company, Inc.</p>
    </footer>
</div> <!-- /container -->
</body>
</html>