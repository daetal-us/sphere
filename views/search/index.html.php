<h1>search results</h1>
<?php if (!empty($q) && empty($results)) { ?>
	<p>No posts matched your query. Need <?php echo $this->html->link('help', '#', array('class' => 'toggle-search-help')); ?>?</p>
<?php } elseif (!empty($results)) { ?>
	<p><?php echo $results->count(); ?> post<?php echo ($results->count() > 1) ? 's' : null; ?> matched your query. Need <?php echo $this->html->link('help', '#', array('class' => 'toggle-search-help')); ?> finding better results?</p>
<?php } else { ?>
	<p>Try typing in your search query in the input to the top right. Need <?php echo $this->html->link('help', '#', array('class' => 'toggle-search-help')); ?>?</p>
<?php } ?>
<aside>
<div class="search-help">
	<section>
	<h2>searching sphere</h2>
	<p>Sphere utilizes a <?php echo $this->html->link('Lucene', 'http://lucene.apache.org/'); ?>-based search engine. We hope you'll find it easy to create complex search queries utilizing familiar, and maybe even a few new techniques, to quickly find the content you're looking for.</p>
	<h3>terms or "phrases"</h3>
	<p>The core of your search query consists of your terms. You can input a single term, or word, or combination of terms to start. Our search engine will return posts with <strong>any</strong> of the terms provided.</p>
	<p>If you are searching for a collection of terms with the order and presence of all terms needing to be exactly required (also called "phrase queries"), surround your terms with single or double-quotes.</p>
	<table>
		<thead>
			<th>sample query</th>
			<th>result</th>
		</thead>
		<tbody>
			<tr>
				<td class="sample-query">lorem</td>
				<td>posts containing `lorem`</td>
			</tr>
			<tr>
				<td class="sample-query">lorem ipsum</td>
				<td>posts containg `lorem` OR `ipsum`</td>
			</tr>
			<tr>
				<td class="sample-query">"lorem ipsum"</td>
				<td>posts containing `lorem ipsum`</td>
			</tr>
			<tr>
				<td class="sample-query">lorem "ipsum dolor"</td>
				<td>posts containing `lorem` OR `ipsum dolor`</td>
			</tr>
		</tbody>
	</table>

	<h3>field: query</h3>
	<p>Our engine contains indexes of particular data relating to each post referred to as fields. You can target a specific field, or even combination of them, for your specific search using the field name followed by colon and your query. For example, <code>title: "some title"</code> would result in posts with "some title" in their title.</p>
	<table>
		<thead>
			<th>field</th>
			<th>description of post data</th>
		</thead>
		<tbody>
			<tr>
				<td>content</td>
				<td>the main body of the post</td>
			</tr>
			<tr>
				<td>title</td>
				<td>the post title</td>
			</tr>
			<tr>
				<td>author</td>
				<td>the post author's username</td>
			</tr>
			<tr>
				<td>tag</td>
				<td>tags that have been associated with the post</td>
			</tr>
			<tr>
				<td>date</td>
				<td>numeric timestamp of post creation date</td>
			</tr>
		</tbody>
	</table>

	<h3>term modifiers*</h3>
	<p>You can configure more advanced search conditions by combining terms with any number of modifiers.</p>
	<table>
		<thead>
			<th>operator</th>
			<th>effect</th>
			<th width="25%">sample query</th>
			<th>result</th>
		</thead>
		<tbody>
			<tr>
				<td><code>AND</code> or <code>&&</code></td>
				<td>terms connected with `AND` both must be present</td>
				<td class="sample-query">lorem AND ipsum<br />lorem && ipsum</td>
				<td>posts containing `lorem` and `ipsum`</td>
			</tr>
			<tr>
				<td><code>OR</code> or <code>||</code></td>
				<td>terms connected with `OR` can include either or both terms being present</td>
				<td class="sample-query">lorem OR ipsum<br />
					lorem || ipsum<br />
					lorem ipsum</td>
				<td>posts containing `lorem` and/or `ipsum`</td>
			</tr>
			<tr>
				<td><code>NOT</code> or <code>!</code></td>
				<td>exclude documents containing the term(s) that follow</td>
				<td class="sample-query">lorem NOT ipsum<br />
					lorem ! ipsum</td>
				<td>posts containing `lorem` but not `ipsum`</td>
			</tr>
			<tr>
				<td><code>+</code></td>
				<td>the term that immediately follows must be present</td>
				<td class="sample-query">lorem +ipsum</td>
				<td>posts containing `ipsum` that may or may not contain `lorem`</td>
			</tr>
			<tr>
				<td><code>-</code></td>
				<td>the term that immediately follows must not be present</td>
				<td class="sample-query">lorem -ipsum</td>
				<td>posts containing `lorem` but not `ipsum`</td>
			</tr>
			<tr>
				<td><code>?</code></td>
				<td>the location of `?` in the term can be replaced with any single character to match <br /><em>wildcard searches can only occur with single terms <strong>(not phrase queries)</strong> and cannot be the first character of a search</em></td>
				<td class="sample-query">lor?m</td>
				<td>posts containing any number of variations such as, `lorem`, `lorim`, `lorrm`, etc.</td>
			</tr>
			<tr>
				<td><code>*</code></td>
				<td>the location of `*` in the term can be replaced with any number of any characters to match <br /><em>wildcard searches can only occur with single terms <strong>(not phrase queries)</strong> and cannot be the first character of a search</em></td>
				<td class="sample-query">lor*</td>
				<td>posts containing any number of variations such as, `lorem`, `lore`, `loremipsum`, etc.</td>
			</tr>
			<tr>
				<td><code>~</code></td>
				<td>perform a fuzzy search of the term based on the <?php echo $this->html->link('Levenshtein distance', 'http://en.wikipedia.org/wiki/Levenshtein_distance'); ?></td>
				<td class="sample-query">lorem~</td>
				<td>posts containing `lorem`, `lirem`, `borem`, `logem` etc.</td>
			</tr>
			<tr>
				<td><code>^1</code></td>
				<td>control the relevance of a document by boosting a term preceded with the `^` and an optional positive number, or boost factor. By default, all terms have a boost factor of 1.</td>
				<td class="sample-query">lorem^2 ipsum dolor^0.5</td>
				<td>posts containing any of `lorem`, `ipsum` and/or `dolor` with `lorem` being the most relevant term, and `dolor` being the least.</td>
			</tr>
			<tr>
				<td><code>\</code></td>
				<td>escape a special character to use as part of a term rather than as a modifier.<br />Special characters are any of the following: <br /><strong>+ - && || ! ( ) { } [ ] ^ " ~ * ? : \</strong></td>
				<td class="sample-query">lorem\: ipsum\*</td>
				<td>posts containing `lorem:` and/or `ipsum*`</td>
			</tr>
		</tbody>
	</table>
	<h3>[min TO max] ranges</h3>
	<p>You can specify lower and upper bounds for query values, and the ranges can be either inclusive or exclusive. Sorting is <?php echo $this->html->link('lexicographical', 'http://en.wikipedia.org/wiki/Lexicographical_order'); ?>.</p>
	<table>
		<thead>
			<th>syntax</th>
			<th>type</th>
			<th width="25%">sample query</th>
			<th>result</th>
		</thead>
		<tbody>
			<tr>
				<td><code>[min TO max]</code></td>
				<td>inclusive</td>
				<td class="sample-query">year:[2009 TO 2010]</td>
				<td>posts created between 2009 and 2010</td>
			</tr>
			<tr>
				<td><code>{min TO max}</code></td>
				<td>exclsuive</td>
				<td class="sample-query">author:{Alex TO Fred}</td>
				<td>posts with author usernames excluding those lexicographically between Alex and Fred</td>
			</tr>
		</tbody>
	</table>
	<h3>(grouping queries)</h3>
	<p>You can group queries to form subqueries using parentheses. This can be really helpful when building multiple field-specific queries.</p>
	<table>
		<thead>
			<th>sample query</th>
			<th>result</th>
		</thead>
		<tbody>
			<tr>
				<td class="sample-query">(lorem OR ipsum) AND dolor</td>
				<td>posts containing `dolor` and either `lorem` and/or `ipsum</td>
			</tr>
			<tr>
				<td class="sample-query">title:(lorem OR ipsum) content:dolor</td>
				<td>posts containing `lorem` or `ipsum` in their title and `dolor` in their content</td>
			</tr>
		</tbody>
	</table>
	<a class="close-search-help" href="#">close help</a>
	</section>
</div>
</aside>

<?php if (!empty($results)) { ?>
	<ul class="posts">
	<?php
		while ($item = $results->current()) {
			echo $this->post->row($item->post());
			$results->next();
		}
	?>
	</ul>
<?php } ?>

<?php $this->viewJs = "
<script type=\"text/javascript\">
	$(document).ready(function() {
		$('.search-help').hide();
		$('.toggle-search-help').toggle(function() {
			$('.search-help').show('slow');
		}, function() {
			$('.search-help').hide('slow');
		});
		$('.close-search-help').click(function() {
			$('.toggle-search-help').click();
			return false;
		});
	});
</script>"; ?>