{root:}
	{data.info:gallery}
{gallery:}
	<div class="phorts-list">
		<style>
			.phorts-list {
				margin-top: 30px;
				margin-left:-5px;
				margin-right:-5px;
			}
			.phorts-list img {
				padding:5px;
				width:20%;
			}
		</style>
		{gallery::bigimg}
	</div>
	<a href="/{parent.crumb}">{parent.config.title}</a>
	<script type="module">
		import { CDN } from '/vendor/akiyatkin/load/CDN.js'
		
		const div = document.getElementById('{div}')
		const cls = (cls, el = div) => el.getElementsByClassName(cls)
		const block = cls('phorts-list')[0]
		const items = cls('gallery', block)
		if (items.length) CDN.fire('load','magnific-popup').then(() => {
			$(div).find('a.gallery').magnificPopup({
				type: 'image',
				gallery:{
					enabled:true
				}
			})
			var hash = location.hash
			if (hash) {
				hash = hash.replace(/^#/, '')
				if (hash == 'show') {
					div.find('a:first').click()
				} else {
					var el = document.getElementById('img-'+hash)
					$(el).click()
				}
			}
		})
	</script>
{bigimg:}<a style="border:none" class="gallery" id="img-{name}" href="/-imager/?src={...gallerydir}{~encode(file)}"><img loading="lazy" style="width:20%" src="/-imager/?w=400&h=300&crop=1&src={~encode(...gallerydir)}{~encode(file)}&top=1"></a>
