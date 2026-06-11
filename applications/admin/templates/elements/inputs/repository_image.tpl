{$repository_image_thumb_size=$thumb_size|default:'200x150'}

<div id="{$id}_preview_wrap" style="display:none; margin-bottom:10px;">
	<img
		id="{$id}_preview"
		alt=""
		style="display:block; max-width:100%; width:auto; height:auto; border:1px solid #ddd; border-radius:3px; object-fit:contain;"
	/>
</div>

{include file="{$smarty.current_dir}/repository.tpl"}

<script>
	require(['gwcms'], function(){
		var input = document.getElementById('{$id}');
		var preview = document.getElementById('{$id}_preview');
		var previewWrap = document.getElementById('{$id}_preview_wrap');
		var thumbSize = '{$repository_image_thumb_size|escape:'javascript'}';
		var sizeMatch = thumbSize.match(/^(\d+)x(\d+)$/i);

		if (!input || !preview || !previewWrap)
			return;

		if (!sizeMatch) {
			thumbSize = '200x150';
			sizeMatch = ['200x150', '200', '150'];
		}

		preview.style.width = sizeMatch[1] + 'px';
		preview.style.height = sizeMatch[2] + 'px';

		function getPreviewUrl(value) {
			value = $.trim(value || '');
			if (!value)
				return '';

			var repositoryMarker = '/repository/';
			var markerPosition = value.indexOf(repositoryMarker);
			if (markerPosition === -1)
				return value;

			var origin = '';
			var absoluteMatch = value.match(/^(https?:\/\/[^/]+)/i);
			if (absoluteMatch)
				origin = absoluteMatch[1];

			var repositoryPath = value.substring(markerPosition + repositoryMarker.length);
			return origin + '/tools/img_resize?file=' + encodeURIComponent(repositoryPath)
				+ '&dirid=repository&size=' + encodeURIComponent(thumbSize);
		}

		function updatePreview() {
			var previewUrl = getPreviewUrl(input.value);
			previewWrap.style.display = previewUrl ? 'block' : 'none';
			if (previewUrl)
				preview.src = previewUrl;
			else
				preview.removeAttribute('src');
		}

		input.addEventListener('input', updatePreview);
		input.addEventListener('change', updatePreview);
		preview.addEventListener('error', function(){
			previewWrap.style.display = 'none';
		});
		preview.addEventListener('load', function(){
			previewWrap.style.display = 'block';
		});

		updatePreview();
	});
</script>
