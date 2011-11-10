<script type="text/javascript">
Ext.onReady(function(){
    Ubmod.app.addStatsPanel({
        store: Ext.create('Ubmod.store.QueueActivity'),
        renderTo: 'stats',
        gridTitle: 'All Queues',
        recordFormat: {
            label: 'Queues',
            key: 'queue',
            id: 'queue_id',
            detailsUrl: '/queue/details'
        }
    });
});
</script>
<div id="stats"></div>
<br/>
<div class="chart-desc">
This table provides detailed information on machine queues, including average
job size, average wait time, and average run time. Clicking once on the
headings in each of the columns will sort the column (Table) from high to low.
A second click will reverse the sort. The Search capability allows you to
search for a particular queue. Press enter in the search bar to filter.
Double-click a row to open a detail tab for that queue.
</div>
