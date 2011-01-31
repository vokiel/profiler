<?php defined('SYSPATH') or die('No direct script access.') ?>

<style type="text/css">
<?php include Kohana::find_file('views', 'profiler/style', 'css') ?>
</style>
<script type="text/javascript">
<?php include Kohana::find_file('views', 'profiler/javascript', 'js') ?>
</script>
<?php
$group_stats      = Profiler::group_stats();
$group_cols       = array('min', 'max', 'average', 'total');
$application_cols = array('min', 'max', 'average', 'current');
?>

<a id="kohana_profiler_show_hide" title="Show/Hide Profiler">Show Profiler</a>
<div id="kohana_profiler" style="display: none;">	
	<?php foreach (Profiler::groups() as $group => $benchmarks): ?>
	<table class="profiler">
		<tr class="group">
			<th class="name" rowspan="2"><?php echo __(ucfirst($group)) ?></th>
			<td class="time" colspan="4"><?php echo number_format($group_stats[$group]['total']['time'], 6) ?> <abbr title="seconds">s</abbr></td>
		</tr>
		<tr class="group">
			<td class="memory" colspan="4"><?php echo Text::bytes($group_stats[$group]['total']['memory']); ?> </td>
		</tr>
		<tr class="headers">
			<th class="name"><?php echo __('Benchmark') ?></th>
			<?php foreach ($group_cols as $key): ?>
			<th class="<?php echo $key ?>"><?php echo __(ucfirst($key)) ?></th>
			<?php endforeach ?>
		</tr>
		<?php foreach ($benchmarks as $name => $tokens): ?>
		<tr class="mark time">
			<?php $stats = Profiler::stats($tokens) ?>
			<th class="name" rowspan="2" scope="rowgroup"><?php echo $name, ' (', count($tokens), ')' ?></th>
			<?php foreach ($group_cols as $key): ?>
			<td class="<?php echo $key ?>">
				<div>
					<div class="value"><?php echo number_format($stats[$key]['time'], 6) ?> <abbr title="seconds">s</abbr></div>
					<?php if ($key === 'total'): ?>
						<div class="graph" style="left: <?php echo max(0, 100 - $stats[$key]['time'] / $group_stats[$group]['max']['time'] * 100) ?>%"></div>
					<?php endif ?>
				</div>
			</td>
			<?php endforeach ?>
		</tr> 
		<tr class="mark memory">
			<?php foreach ($group_cols as $key): ?>
			<td class="<?php echo $key ?>">
				<div>
					<div class="value"><?php echo Text::bytes($stats[$key]['memory']); ?></div>
					<?php if ($key === 'total'): ?>
						<div class="graph" style="left: <?php echo max(0, 100 - $stats[$key]['memory'] / $group_stats[$group]['max']['memory'] * 100) ?>%"></div>
					<?php endif ?>
				</div>
			</td>
			<?php endforeach ?>
		</tr>
		<?php endforeach ?>
	</table>
	<?php endforeach ?>

	<table class="profiler">
		<?php $stats = Profiler::application() ?>
		<tr class="final mark time">
			<th class="name" rowspan="2" scope="rowgroup"><?php echo __('Application Execution').' ('.$stats['count'].')' ?></th>
			<?php foreach ($application_cols as $key): ?>
			<td class="<?php echo $key ?>"><?php echo number_format($stats[$key]['time'], 6) ?> <abbr title="seconds">s</abbr></td>
			<?php endforeach ?>
		</tr>
		<tr class="final mark memory">
			<?php foreach ($application_cols as $key): ?>
			<td class="<?php echo $key ?>"><?php echo Text::bytes($stats[$key]['memory']); ?></td>
			<?php endforeach ?>
		</tr>
	</table>
	<?php $error_id = uniqid('error');?>
     <table class="profiler">
		<?php foreach (array('_SESSION', '_GET', '_POST', '_FILES', '_COOKIE') as $var): ?>
		<?php if (empty($GLOBALS[$var]) OR ! is_array($GLOBALS[$var])) continue ?>
		<tr class="final mark"><th class="name" colspan="2"><?php echo $var; ?></th></tr>
				<?php foreach ($GLOBALS[$var] as $key => $value): ?>
				<tr>
					<td style="text-align:right;"><code><?php echo $key ?></code></td>
					<td style="text-align:left;"><pre><?php echo Kohana::dump($value) ?></pre></td>
				</tr>
				<?php endforeach ?>
		<?php endforeach ?>
	</table>
	<?php if (Profiler::$server) { ?>
     <table class="profiler">
		<?php foreach (array('_SERVER') as $var): ?>
		<?php if (empty($GLOBALS[$var]) OR ! is_array($GLOBALS[$var])) continue ?>
		<tr class="final mark"><th class="name" colspan="2"><?php echo $var; ?></th></tr>
				<?php foreach ($GLOBALS[$var] as $key => $value): ?>
				<tr>
					<td style="text-align:right;"><code><?php echo $key ?></code></td>
					<td style="text-align:left;"><pre><?php echo Kohana::dump($value) ?></pre></td>
				</tr>
				<?php endforeach ?>
		<?php endforeach ?>
	</table>
	<?php } ?>
	<?php if (Profiler::$included) { ?>
     <table class="profiler">
          <?php $included = get_included_files() ?>
     	<tr class="final mark"><th class="name" colspan="2"><?php echo __('Included files') ?></a> (<?php echo count($included) ?>)</th></tr>
		<?php foreach ($included as $file): ?>
				<tr>
					<td style="text-align:left;"><code><?php echo Kohana::debug_path($file) ?></code></td>
				</tr>
		<?php endforeach ?>
	</table>
	<?php } ?>
	<?php if (Profiler::$extensions) { ?>
     <table class="profiler">
          <?php $included = get_loaded_extensions() ?>
     	<tr class="final mark"><th class="name" colspan="2"><?php echo __('Loaded extensions') ?></a> (<?php echo count($included) ?>)</th></tr>
		<?php foreach ($included as $file): ?>
				<tr>
					<td style="text-align:left;"><code><?php echo Kohana::debug_path($file) ?></code></td>
				</tr>
		<?php endforeach ?>
	</table>
	<?php } ?>
</div>