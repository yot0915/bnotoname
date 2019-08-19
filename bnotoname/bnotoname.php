<?php
session_start();

?>
<!doctype html>
<html lang="en-CA" prefix="og: http://ogp.me/ns#" class="no-js gdpr-opted-in">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<script>
		var checkForm = function(myform)
		{
			if(isNaN(myform.bno.value))
			{
				alert('請輸入數字');
				return false;
			}	
			return true;
		}	
		</script>
    </head>
    <body>
        <form method='post' onSubmit="return checkForm(this);">
            <input type='text' name='bno' placeholder="請輸入統編" maxlength=8 minlength=8 size=8>
            <input type='submit' value="查詢公司名稱">
			
<?php
if(isset($_POST['bno']))
{
	if(isset($_SESSION['csrf']) &&isset($_POST['token']) && $_SESSION['csrf']!=$_POST['token'])
	{
		echo $_POST['token'];
		die('??');
	}	
	
	
	if(trim($_POST["bno"])<>''&&is_numeric($_POST['bno'])&&strlen($_POST['bno'])==8 && isset($_SESSION['csrf']) &&isset($_POST['token']) && $_SESSION['csrf']==$_POST['token'] )
	{
		echo "統編 : ".htmlspecialchars($_POST['bno']).'<br />';
		//$url='http://localhost/bnotoname/test.json';
		$url='http://data.gcis.nat.gov.tw/od/data/api/9D17AE0D-09B5-4732-A8F4-81ADED04B679?$format=json&$filter='.urlencode('Business_Accounting_NO eq '.basename($_POST["bno"])).'&$skip=0&$top=1';
		echo "api url : ".$url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		
		$UserAgent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
	
		curl_setopt($ch, CURLOPT_USERAGENT, $UserAgent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		$output = curl_exec($ch);
		curl_close($ch);
		$objBno= json_decode($output);
		if(!$objBno)
		{
			echo "<br />錯誤訊息:".htmlspecialchars($output) ;
		}	
		else
		{
			if(isset($objBno[0]))
			{
				$objBno = $objBno[0];
			}
			
			echo $objBno->Company_Name;	
		}
		unset($_SESSION['csrf']);
	}	
	else
	{
		echo '請輸入正確統編';
	}	
}	


if(!isset($_SESSION['csrf']))
{
	$_SESSION['csrf']=sha1(microtime());
}	
?>
  <input type="hidden" name="token" value="<?=$_SESSION['csrf']?>" />
        </form>
    </body>      
    
</html>
    