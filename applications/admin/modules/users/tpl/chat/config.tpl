{call e field=chatws_enabled type=bool note="Override shared CHATWS/ENABLED for this project"}
{call e field=onlinechat_widget_enabled type=bool note="Show online chat users widget in admin sidebar"}
{call e field=autorestart_on_version_change type=bool default=0 note="Restart ReactPHP chat server when ROOT_DIR/version changes"}
{call e field=wss_log_to_console type=bool default=0 note="Log chat websocket events to browser console"}
{call e field=full_chat_debug type=bool default=0 note="Enable full chat debug: browser console, backend timings, ReactPHP packet debug"}
{call e field=push_private_enabled type=bool default=0 note="Send browser push notifications for private chat messages when recipient is offline"}
{call e field=push_private_offline_after_seconds type=number default=90 note="Recipient is considered offline after this many seconds since last request, unless live WS is connected"}
{call e field=push_private_room_cooldown_seconds type=number default=180 note="Minimum seconds between private chat push notifications for the same recipient and room"}
{call e field=push_private_preview_enabled type=bool default=1 note="Include a short message preview in private chat push notifications"}
{call e field=push_private_preview_max_length type=number default=120 note="Maximum private chat push preview length"}
{call e field=push_private_quiet_hours_enabled type=bool default=0 note="Suppress private chat push notifications during quiet hours"}
{call e field=push_private_quiet_hours_from default='22:00' note="Quiet hours start time, HH:MM"}
{call e field=push_private_quiet_hours_to default='08:00' note="Quiet hours end time, HH:MM"}

{call e field=attachment_storage type=select options=['local'=>'Local repository','voro1'=>'1.voro.lt mirror'] default='local' note="Choose where new chat uploads are stored. voro1 uses current host + .1.voro.lt/tools/chat_store unless endpoint below is set."}
{call e field=remote_store_token note="Required on both sides when storage is 1.voro.lt mirror. Keep the same token in production and mirror."}
{call e field=remote_store_endpoint note="Optional override. Empty uses current host + .1.voro.lt/tools/chat_store"}
{call e field=allowed_extensions default='pdf,doc,docx,docm,dot,dotx,odt,ott,rtf,txt,csv,xls,xlsx,xlsm,ods,ots,ppt,pptx,pptm,odp,otp,jpg,jpeg,png,gif,webp,bmp,tif,tiff,heic,heif,zip,rar,7z' note="Comma separated. Extension is checked from original filename."}
{call e field=allowed_attachment_size default='10' note="Max attachment size. Plain number means MB; also accepts 10MB, 512KB, 1GB."}
{call e field=max_files_per_message default='5' type=number note="Max files in one chat message"}
