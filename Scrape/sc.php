<?php
	include('simple_html_dom.php');
	$prices_original = array();
	// function to get the prices for the first time
	function get_original_price()
	{
		global $prices_original;
		$html = file_get_html("https://www.shopclues.com/search?q=4g%20smartphones&sc_z=&z=1&count=40");
		foreach ($html->find('.search_blocks') as $block) {
			foreach ($block->find('.p_price') as $price) {
				$prices_original [] = (int)substr($price->plaintext, 3);
			}
		}
	}
	get_original_price();
	function scrape_display($check_price)
	{
		global $prices_original;
		$html = file_get_html("https://www.shopclues.com/search?q=4g%20smartphones&sc_z=&z=1&count=40");
		$names = array();
		$prices = array();
		$discounts = array();
		$images = array();
		$links = array();
		$len = 0;
		foreach ($html->find('.search_blocks h2') as $title) {
			$names [] = $title->plaintext;
			$len++;
		}
		foreach ($html->find('.search_blocks') as $block) {
			foreach ($block->find('.p_price') as $price) {
				$prices [] = (int)substr($price->plaintext, 3);
			}
			foreach ($block->find('a') as $link) {
				$links [] = $link->href;
			}
			foreach($block->find('.prd_discount') as $discount)
			{
				$discounts [] = $discount->plaintext;
			}
			foreach($block->find('img') as $img)
			{
				$images [] = $img->src;
			}
		}
		// make a string to display the page
		$display_var = '<table><tr><th>Icon</th><th>Name</th><th>Price</th><th>Offer</th></tr>';
		for($i = 0; $i < $len; $i++)
		{
			$display_var .= '<tr>';
			$display_var .=	"<td><a href='$links[$i]'><img src='$images[$i]'/></a></td>";
			$display_var .= "<td>$names[$i]</td>";
			$display_var .= "<td>Rs.$prices[$i]</td>";
			$display_var .= "<td>$discounts[$i]</td>";
			$display_var .= "</tr>";
		}
		//check for the price drop
		if($check_price == 1)
		{
			for($i = 0; $i < $len; $i++)
			{
				if($prices[$i] < $prices_original[$i])
				{
					$subject = $names[$i];
					$message = "Check out this ".$links[$i];
					$to = "admin@example.abc"; //------------------------use your email below
					mail($to, $subject, $message);
				}
			}
		}
		$display_var .= '</table>';
		return $display_var;
	}	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Assignment</title>
	<style>
		*
		{
			font-family: helvetica;
		}
		td
		{
			padding: 10px;
		}
	</style>
</head>
<body>
		<div id="my_div"></div>
		<script>
			//call it for the fist time to render the results on the screen
			printIt();
			function printIt()
			{
				console.log('called');
				document.getElementById('my_div').innerHTML = "<?php echo scrape_display(1); ?>";
			}
			// call after 60 secs with check_price = 1
			setInterval(printIt, 60000);
		</script>
</body>
</html>