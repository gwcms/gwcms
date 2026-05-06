UPDATE gw_mail_templates
SET
	body_lt='<!DOCTYPE html>
<html lang="lt">
<head>
<style>
table.res-table { border-collapse: collapse; width: 100%; }
table.res-table td { border: 1px solid #ddd; }
</style>
<meta charset="UTF-8" />
<title>{$COMPETITION_TITLE} - rezervacijos viešbučiui {$HOTEL_TITLE}</title>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding:30px 0;background-color:#e9edf3;background-image:url("https://www.transparenttextures.com/patterns/subtle-dots.png");background-repeat:repeat;">
	<tbody>
		<tr>
			<td align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="600" style="background:#ffffff;border-radius:10px;overflow:hidden;">
					<tbody>
						<tr>
							<td style="padding:20px 30px;border-bottom:2px solid #1f3c88;">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tbody>
										<tr>
											<td align="right" width="33%">{if $COMPETITION_IMAGE}<img src="{$COMPETITION_IMAGE}" style="max-width:150px;max-height:150px;width:auto;height:auto;" />{/if}</td>
											<td align="center" width="34%"><img src="https://artistdb.eu/repository/tkpc_lt.jpg" style="max-width:150px;max-height:150px;width:auto;height:auto;" /></td>
											<td align="left" width="33%"><img src="https://artistdb.eu/repository/Menu_logo_new2026.png" style="max-width:150px;max-height:150px;width:auto;height:auto;" /></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style="padding:30px;text-align:center;">
								<div style="font-size:24px;font-weight:bold;color:#1f3c88;letter-spacing:1px;">VIEŠBUČIO REZERVACIJOS</div>
								<div style="margin-top:10px;font-size:14px;color:#666;">{$COMPETITION_TITLE}</div>
							</td>
						</tr>
						<tr>
							<td align="center">
								<div style="background:#1f3c88;color:#ffffff;display:inline-block;padding:10px 22px;font-size:14px;letter-spacing:1px;border-radius:4px;"><b>{$HOTEL_TITLE}</b></div>
							</td>
						</tr>
						<tr>
							<td style="padding:30px 30px 0 30px;font-size:14px;color:#444;">
								<p style="margin:0 0 10px 0;">Gerb. {$HOTEL_TITLE},</p>
								<p style="margin:0;">Siunčiame kambarių užimtumo ir rezervacijų sąrašą.</p>
							</td>
						</tr>
						{foreach $HOTEL_RESERVATION_GROUPS as $period}
						<tr>
							<td style="padding:30px 30px 0 30px;">
								<table class="res-table" cellpadding="8" cellspacing="0">
									<tbody>
										<tr>
											<td colspan="4" align="center" style="padding-bottom:12px;font-size:16px;font-weight:bold;color:#1f3c88;border-bottom:1px solid #e6edf7;">Gyvenimo laikotarpis: {$period.STAY_PERIOD}</td>
										</tr>
										{foreach $period.ROOM_TYPES as $room_type}
										<tr>
											<td colspan="4" style="background:#f4f7fb;color:#1f3c88;font-weight:bold;">Kambario tipas {$room_type.ROOM_TYPE_TITLE} - {$room_type.ROOM_COUNT} {$room_type.ROOM_COUNT_WORD}{if $room_type.ROOM_TITLES_TEXT}<br /><span style="font-size:12px;color:#555;font-weight:normal;">Kambarys: {$room_type.ROOM_TITLES_TEXT}</span>{/if}</td>
										</tr>
										{foreach $room_type.GUESTS as $guest}
										<tr>
											<td style="color:#777;">Svečiai</td>
											<td style="color:#1f3c88;"><b>{$guest.GUEST_NAMES}</b></td>
											<td style="color:#777;">Kambarys</td>
											<td>{if $guest.ROOM_TITLE}{$guest.ROOM_TITLE}{else}-{/if}</td>
										</tr>
										{/foreach}
										{/foreach}
									</tbody>
								</table>
							</td>
						</tr>
						{/foreach}
						<tr>
							<td style="padding:30px;">
								<table width="100%" style="background:#f4f7fb;border-left:4px solid #1f3c88;">
									<tbody>
										<tr>
											<td style="padding:15px;font-size:13px;color:#444;"><b style="color:#1f3c88;">Pastabos:</b><br />Rezervacijų skaičius: {$HOTEL_RESERVATION_COUNT}. Prašome patvirtinti gavimą.</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style="padding:20px 30px;text-align:center;font-size:12px;color:#888;border-top:1px solid #eee;">Pagarbiai,<br />Menų turas</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
</body>
</html>',
	body_en='<!DOCTYPE html>
<html lang="en">
<head>
<style>
table.res-table { border-collapse: collapse; width: 100%; }
table.res-table td { border: 1px solid #ddd; }
</style>
<meta charset="UTF-8" />
<title>{$COMPETITION_TITLE} - accommodation reservations for {$HOTEL_TITLE}</title>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding:30px 0;background-color:#e9edf3;background-image:url("https://www.transparenttextures.com/patterns/subtle-dots.png");background-repeat:repeat;">
	<tbody>
		<tr>
			<td align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="600" style="background:#ffffff;border-radius:10px;overflow:hidden;">
					<tbody>
						<tr>
							<td style="padding:20px 30px;border-bottom:2px solid #1f3c88;">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tbody>
										<tr>
											<td align="right" width="33%">{if $COMPETITION_IMAGE}<img src="{$COMPETITION_IMAGE}" style="max-width:150px;max-height:150px;width:auto;height:auto;" />{/if}</td>
											<td align="center" width="34%"><img src="https://artistdb.eu/repository/tkpc_lt.jpg" style="max-width:150px;max-height:150px;width:auto;height:auto;" /></td>
											<td align="left" width="33%"><img src="https://artistdb.eu/repository/Menu_logo_new2026.png" style="max-width:150px;max-height:150px;width:auto;height:auto;" /></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style="padding:30px;text-align:center;">
								<div style="font-size:24px;font-weight:bold;color:#1f3c88;letter-spacing:1px;">HOTEL RESERVATIONS</div>
								<div style="margin-top:10px;font-size:14px;color:#666;">{$COMPETITION_TITLE}</div>
							</td>
						</tr>
						<tr>
							<td align="center">
								<div style="background:#1f3c88;color:#ffffff;display:inline-block;padding:10px 22px;font-size:14px;letter-spacing:1px;border-radius:4px;"><b>{$HOTEL_TITLE}</b></div>
							</td>
						</tr>
						<tr>
							<td style="padding:30px 30px 0 30px;font-size:14px;color:#444;">
								<p style="margin:0 0 10px 0;">Dear {$HOTEL_TITLE},</p>
								<p style="margin:0;">Please find below the room occupancy and reservation list.</p>
							</td>
						</tr>
						{foreach $HOTEL_RESERVATION_GROUPS as $period}
						<tr>
							<td style="padding:30px 30px 0 30px;">
								<table class="res-table" cellpadding="8" cellspacing="0">
									<tbody>
										<tr>
											<td colspan="4" align="center" style="padding-bottom:12px;font-size:16px;font-weight:bold;color:#1f3c88;border-bottom:1px solid #e6edf7;">Stay period: {$period.STAY_PERIOD}</td>
										</tr>
										{foreach $period.ROOM_TYPES as $room_type}
										<tr>
											<td colspan="4" style="background:#f4f7fb;color:#1f3c88;font-weight:bold;">Room type {$room_type.ROOM_TYPE_TITLE} - {$room_type.ROOM_COUNT} rooms{if $room_type.ROOM_TITLES_TEXT}<br /><span style="font-size:12px;color:#555;font-weight:normal;">Room: {$room_type.ROOM_TITLES_TEXT}</span>{/if}</td>
										</tr>
										{foreach $room_type.GUESTS as $guest}
										<tr>
											<td style="color:#777;">Guests</td>
											<td style="color:#1f3c88;"><b>{$guest.GUEST_NAMES}</b></td>
											<td style="color:#777;">Room</td>
											<td>{if $guest.ROOM_TITLE}{$guest.ROOM_TITLE}{else}-{/if}</td>
										</tr>
										{/foreach}
										{/foreach}
									</tbody>
								</table>
							</td>
						</tr>
						{/foreach}
						<tr>
							<td style="padding:30px;">
								<table width="100%" style="background:#f4f7fb;border-left:4px solid #1f3c88;">
									<tbody>
										<tr>
											<td style="padding:15px;font-size:13px;color:#444;"><b style="color:#1f3c88;">Notes:</b><br />Reservation count: {$HOTEL_RESERVATION_COUNT}. Please confirm receipt.</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style="padding:20px 30px;text-align:center;font-size:12px;color:#888;border-top:1px solid #eee;">Best regards,<br />Menu turas</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
</body>
</html>',
	variables='COMPETITION_TITLE, COMPETITION_IMAGE, HOTEL_TITLE, HOTEL_NAME, HOTEL_ADDRESS, HOTEL_RESERVATION_COUNT, HOTEL_RESERVATION_GROUPS, ROOM_TITLE, ROOM_TITLES, ROOM_TITLES_TEXT',
	protected=1
WHERE idname='mail-reservations-2hotels';
