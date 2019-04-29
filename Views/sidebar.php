<table class="table table-condensed" id="feeds"></table>

<div id="mygraphs">
    <h4><a href="#" class="collapsed" data-toggle="collapse" data-target="#graph-list">
        <?php echo _('My Graphs') ?> <span class="arrow arrow-down pull-right"></span>
    </a></h4>

    <div id="graph-list" class="collapse">
        <select id="graph-select"></select>
        <h5><?php echo _('Graph Name') ?>:</h5>
        <input id="graph-name" type="text">
        <small id="selected-graph-id"><?php echo _('Selected graph id') ?>: <span id="graph-id"><?php echo _('None selected') ?></span></small>
        <button id="graph-delete" class="btn" style="display:none"><?php echo _('Delete') ?></button>
        <button id="graph-save" class="btn"><?php echo _('Save') ?></button>
    </div>
</div>