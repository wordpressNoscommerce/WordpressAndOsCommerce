<?php
  require('wp-setupFramework.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ACCOUNT_SUCCESS);
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<div id="scrollTextContent">

				<h1>
					<?php echo HEADING_TITLE; ?>
				</h1>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBox">
								<tr>
									<td valign="top">
										<p>
											<?php echo TEXT_ACCOUNT_CREATED; ?>
										</p>
									</td>
								</tr>
							</table></td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
								<tr>
									<td><table border="0" width="100%" cellspacing="0" cellpadding="2">
											<tr>
												<td width="10"></td>
												<td align="right"><button id="acct-created-continue" type="submit" class="button">Continue</button> <?php // echo '<a href="' . $origin_href . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?>
												</td>
												<td width="10"></td>
											</tr>
										</table></td>
								</tr>
							</table></td>
					</tr>
				</table>

			</div>
		</td>
	</tr>
</table>
