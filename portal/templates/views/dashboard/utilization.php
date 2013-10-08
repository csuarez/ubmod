<script type="text/javascript">
Ext.onReady(function () {

    var params = <?php echo $params ?>,
        currentType = 'pie',
        link = Ext.get('swap-link'),
        pie = Ext.select('.pie'),
        bar = Ext.select('.bar');

    pie.each(function (el) { el.setVisibilityMode(Ext.Element.DISPLAY); });
    bar.each(function (el) { el.setVisibilityMode(Ext.Element.DISPLAY); });

    link.on('click', function (e) {
        e.preventDefault();

        if (currentType === 'bar') {
            bar.each(function (el) { el.hide(); });
            pie.each(function (el) { el.show(); });
            currentType = 'pie';
            link.dom.innerHTML = 'Bar';
        } else if (currentType === 'pie') {
            pie.each(function (el) { el.hide(); });
            bar.each(function (el) { el.show(); });
            currentType = 'bar';
            link.dom.innerHTML = 'Pie';
        }
    });

    Ubmod.app.loadChart('user-pie',  'user',  'pie', params);
    Ubmod.app.loadChart('user-bar',  'user',  'bar', params);
    Ubmod.app.loadChart('group-pie', 'group', 'pie', params);
    Ubmod.app.loadChart('group-bar', 'group', 'bar', params);

    <?php if ($interval['multi_month']): ?>
        Ubmod.app.loadChart('user-stacked-area',  'user',  'stackedArea',
            params);
        Ubmod.app.loadChart('group-stacked-area', 'group', 'stackedArea',
            params);
        Ubmod.app.loadChart('user-storage-stacked-area', 'user', 'storageStackedArea',
            params);
        Ubmod.app.loadChart('user-inodes-stacked-area', 'user', 'inodesStackedArea',
            params);
    <?php endif; ?>
});
</script>
<div class="labelHeading" style="font-weight:bold;">
  Utilization for
  <?php if (isset($clusterName)): ?>
    cluster: <?php echo $clusterName ?>,
  <?php else: ?>
    all clusters,
  <?php endif; ?>
  for period
  from: <?php echo $interval['start'] ?> to: <?php echo $interval['end'] ?>
</div>
<div class="chart-desc" style="font-weight:bold;">
  Overall Statistics
</div>
<table class="dtable">
  <tr>
    <th>Users:</th>
    <td style="font-weight:bold;"><?php echo $activity['user_count'] ?></td>
    <th>Total Jobs:</th>
    <td style="font-weight:bold;"><?php echo number_format($activity['jobs']) ?></td>
    <th>Avg. Wall Time (d):</th>
    <td style="font-weight:bold;"><?php echo $activity['avg_wallt'] ?></td>
    <th>Avg. Wait Time (h):</th>
    <td style="font-weight:bold;"><?php echo $activity['avg_wait'] ?></td>
  </tr>
  <tr>
    <th>Groups:</th>
    <td style="font-weight:bold;"><?php echo $activity['group_count'] ?></td>
    <th>Avg. Job Size (CPUs):</th>
    <td style="font-weight:bold;"><?php echo $activity['avg_cpus'] ?></td>
    <th>Avg. Job Size (Nodes):</th>
    <td style="font-weight:bold;"><?php echo $activity['avg_nodes'] ?></td>
    <th>Avg. Exec Time (h):</th>
    <td style="font-weight:bold;"><?php echo $activity['avg_exect'] ?></td>
  </tr>
</table>
<div>
  <table>
    <tr>
      <td colspan="2" style="font-size:x-small;">Plot format:
        <a id="swap-link" class="editLink" href="#">Bar</a></td>
    </tr>
      
    <tr>
      <td style="vertical-align:top;"><img id="user-pie" class="pie" src="<?php echo $BASE_URL ?>/images/loading.gif" /><img id="user-bar" class="bar" style="display:none;" src="<?php echo $BASE_URL ?>/images/loading.gif" /></td>
    </tr>
    <tr>
      <td style="vertical-align:top;"><img id="group-pie" class="pie" src="<?php echo $BASE_URL ?>/images/loading.gif" /><img id="group-bar" class="bar" style="display:none;" src="<?php echo $BASE_URL ?>/images/loading.gif" /></td>
    </tr>
    <?php if ($interval['multi_month']): ?>
     <tr>
        <td style="vertical-align:top;"><img id="user-storage-stacked-area" src="<?php echo $BASE_URL ?>/images/loading.gif" /></td>
      </tr>
        <tr>
        <td style="vertical-align:top;"><img id="user-inodes-stacked-area" src="<?php echo $BASE_URL ?>/images/loading.gif" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top;"><img id="user-stacked-area" src="<?php echo $BASE_URL ?>/images/loading.gif" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top;"><img id="group-stacked-area" src="<?php echo $BASE_URL ?>/images/loading.gif" /></td>
      </tr>
     
    <?php endif; ?>
  </table>
</div>
<div class="chart-desc">
  These plots provide a quick snapshot of machine utilization. Data is
  presented in either Pie or Bar chart format. In the Pie chart format, the
  utilization is given as a percentage of total CPU days consumed and in the
  Bar chart format in total CPU days consumed.
</div>
<div class="chart-desc">
  If the selected time period spans multiple months, stacked area charts are
  displayed with the total CPU days consumed for each month that is included
  in the time period.
</div>
<div class="chart-desc">
  A summary table is also included that provides detailed overall statistics,
  such as the total number of jobs submitted, the average wait time, and the
  average length of a job.
</div>

