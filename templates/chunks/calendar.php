<div id="kronolithCalendarDialog" class="kronolithDialog">

<form id="kronolithCalendarForminternal" action="">
<input id="kronolithCalendarType" type="hidden" name="type" value="internal" />
<input id="kronolithCalendarinternalId" type="hidden" name="calendar" />

<div class="kronolithCalendarDiv" id="kronolithCalendarinternal1">
<div>
  <label><?php echo _("Calendar title") ?>:<br />
    <input type="text" name="name" id="kronolithCalendarinternalName" class="kronolithLongField" />
  </label>
</div>

<div>
  <label><?php echo _("Color") ?>:
    <input type="text" name="color" id="kronolithCalendarinternalColor" size="7" />
    <?php echo Horde::url('#')->link(array('title' => _("Color Picker"), 'class' => 'kronolithColorPicker')) . Horde::img('colorpicker.png', _("Color Picker"), '', $GLOBALS['registry']->getImageDir('horde')) . '</a>' ?>
  </label>
</div>

<div class="tabset">
  <ul>
    <li class="activeTab"><a href="#" class="kronolithTabLink" id="kronolithCalendarinternalLinkDescription"><?php echo _("Description") ?></a></li>
    <li><a href="#" class="kronolithTabLink" id="kronolithCalendarinternalLinkTags"><?php echo _("Tags") ?></a></li>
    <li><a href="#" class="kronolithTabLink" id="kronolithCalendarinternalLinkPerms"><?php echo _("Permissions") ?></a></li>
    <li><a href="#" class="kronolithTabLink" id="kronolithCalendarinternalLinkImportExport"><?php echo _("Export") /*_("Import/Export")*/ ?></a></li>
  </ul>
</div>
<br class="clear" />

<div id="kronolithCalendarinternalTabDescription" class="kronolithTabsOption">
  <textarea name="description" id="kronolithCalendarinternalDescription" rows="5" cols="40" class="kronolithLongField"></textarea>
</div>

<div id="kronolithCalendarinternalTabTags" class="kronolithTabsOption kronolithTabTags" style="display:none">
  <input id="kronolithCalendarinternalTags" name="tags" />
  <span id="kronolithCalendarinternalTags_loading_img" style="display:none;"><?php echo Horde::img('loading.gif', _("Loading...")) ?></span>
  <div class="kronolithTopTags" id="kronolithCalendarinternalTopTags"></div>
</div>

<div id="kronolithCalendarinternalTabPerms" class="kronolithTabsOption" style="display:none">
tbd
</div>

<div id="kronolithCalendarinternalTabImportExport" class="kronolithTabsOption" style="display:none">
  <div class="kronolithTabInfo"><?php echo _("iCalendar is a computer file format which allows internet users to send meeting requests and tasks to other internet users, via email, or sharing files with an extension of .ics. Recipients of the iCalendar data file (with supporting software, such as an email client or calendar application) can respond to the sender easily or counter propose another meeting date/time.") ?></div>
  <?php /* ?>
  <label><?php echo _("Import ICS file") ?>:
    <input type="file" name="import_file" />
  </label>
  <br />
  <?php */ ?>
  <label><?php echo _("Export ICS file") ?>:</label>
  <a id="kronolithCalendarinternalExport"><?php echo _("Calendar ICS file") ?></a>
</div>

<div class="kronolithFormActions">
  <input type="button" value="<?php echo _("Save") ?>" class="kronolithCalendarSave button ok" />
  <input type="button" value="<?php echo _("Delete") ?>" class="kronolithCalendarDelete button ko" />
  <span class="kronolithSeparator"><?php echo _("or") ?></span> <a class="kronolithFormCancel"><?php echo _("Cancel") ?></a>
</div>
</div>

</form>

<form id="kronolithCalendarFormtasklists" action="">
<input id="kronolithCalendarType" type="hidden" name="type" value="tasklists" />
<input id="kronolithCalendartasklistsId" type="hidden" name="calendar" />

<div class="kronolithCalendarDiv" id="kronolithCalendartasklists1">
<div>
  <label><?php echo _("Name") ?>:<br />
    <input type="text" name="name" id="kronolithCalendartasklistsName" class="kronolithLongField" />
  </label>
</div>

<div>
  <label><?php echo _("Color") ?>:<br />
    <input type="text" name="color" id="kronolithCalendartasklistsColor" size="7" />
    <?php echo Horde::url('#')->link(array('title' => _("Color Picker"), 'onclick' => 'new ColorPicker({ color: $F(\'kronolithCalendartasklistsColor\'), offsetParent: Event.element(event), update: [[\'kronolithCalendartasklistsColor\', \'value\'], [\'kronolithCalendartasklistsColor\', \'background\']] }); return false;')) . Horde::img('colorpicker.png', _("Color Picker"), '', $GLOBALS['registry']->getImageDir('horde')) . '</a>' ?>
  </label>
</div>

<div class="tabset">
  <ul>
    <li class="activeTab"><a href="#" class="kronolithTabLink" id="kronolithCalendartasklistsLinkDescription"><?php echo _("Description") ?></a></li>
    <li><a href="#" class="kronolithTabLink" id="kronolithCalendartasklistsLinkPerms"><?php echo _("Permissions") ?></a></li>
    <li><a href="#" class="kronolithTabLink" id="kronolithCalendartasklistsLinkImportExport"><?php echo _("Export") /*_("Import/Export")*/ ?></a></li>
  </ul>
</div>
<br class="clear" />

<div id="kronolithCalendartasklistsTabDescription" class="kronolithTabsOption">
  <textarea name="description" id="kronolithCalendartasklistsDescription" rows="5" cols="40" class="kronolithLongField"></textarea>
</div>

<div id="kronolithCalendartasklistsTabPerms" class="kronolithTabsOption" style="display:none">
tbd
</div>

<div id="kronolithCalendartasklistsTabImportExport" class="kronolithTabsOption" style="display:none">
  <div class="kronolithTabInfo"><?php echo _("iCalendar is a computer file format which allows internet users to send meeting requests and tasks to other internet users, via email, or sharing files with an extension of .ics. Recipients of the iCalendar data file (with supporting software, such as an email client or calendar application) can respond to the sender easily or counter propose another meeting date/time.") ?></div>
  <?php /* ?>
  <label><?php echo _("Import ICS file") ?>:
    <input type="file" name="import_file" />
  </label>
  <br />
  <?php */ ?>
  <label><?php echo _("Export ICS file") ?>:</label>
  <a id="kronolithCalendartasklistsExport"><?php echo _("Calendar ICS file") ?></a>
</div>

<div class="kronolithFormActions">
  <input type="button" value="<?php echo _("Save") ?>" class="kronolithCalendarSave button ok" />
  <input type="button" value="<?php echo _("Delete") ?>" class="kronolithCalendarDelete button ko" />
  <span class="kronolithSeparator"><?php echo _("or") ?></span> <a class="kronolithFormCancel"><?php echo _("Cancel") ?></a>
</div>
</div>

</form>

<form id="kronolithCalendarFormremote" action="">
<input id="kronolithCalendarType" type="hidden" name="type" value="remote" />
<input id="kronolithCalendarremoteId" type="hidden" name="calendar" />

<div class="kronolithCalendarDiv" id="kronolithCalendarremote1">
<div>
  <label><?php echo _("URL") ?>:<br />
    <input type="text" name="url" id="kronolithCalendarremoteUrl" class="kronolithLongField" />
  </label>
</div>

<div>
  <label><?php echo _("Color") ?>:<br />
    <input type="text" name="color" id="kronolithCalendarremoteColor" size="7" />
    <?php echo Horde::url('#')->link(array('title' => _("Color Picker"), 'onclick' => 'new ColorPicker({ color: $F(\'kronolithCalendarremoteColor\'), offsetParent: Event.element(event), update: [[\'kronolithCalendarremoteColor\', \'value\'], [\'kronolithCalendarremoteColor\', \'background\']] }); return false;')) . Horde::img('colorpicker.png', _("Color Picker"), '', $GLOBALS['registry']->getImageDir('horde')) . '</a>' ?>
  </label>
</div>

<div class="kronolithFormActions">
  <input type="button" value="<?php echo _("Continue") ?>" class="kronolithCalendarContinue button ok" />
  <input type="button" value="<?php echo _("Delete") ?>" class="kronolithCalendarDelete button ko" />
  <span class="kronolithSeparator"><?php echo _("or") ?></span> <a class="kronolithFormCancel"><?php echo _("Cancel") ?></a>
</div>
</div>

<div class="kronolithCalendarDiv" id="kronolithCalendarremote2">
<div><?php echo _("This calendar requires to specify a user name and password.") ?></div>

<div>
  <label><?php echo _("Username") ?>:<br />
    <input type="text" name="username" id="kronolithCalendarremoteUsername" class="kronolithLongField" />
  </label>
</div>

<div>
  <label><?php echo _("Password") ?>:<br />
    <input type="password" name="password" id="kronolithCalendarremotePassword" class="kronolithLongField" />
  </label>
</div>

<div class="kronolithFormActions">
  <input type="button" value="<?php echo _("Continue") ?>" class="kronolithCalendarContinue button ok" />
  <span class="kronolithSeparator"><?php echo _("or") ?></span> <a class="kronolithFormCancel"><?php echo _("Cancel") ?></a>
</div>
</div>

<div class="kronolithCalendarDiv" id="kronolithCalendarremote3">
<div>
  <label><?php echo _("Name") ?>:<br />
    <input type="text" name="name" id="kronolithCalendarremoteName" class="kronolithLongField" />
  </label>
</div>

<div>
  <label><?php echo _("Description") ?>:<br />
    <textarea name="description" id="kronolithCalendarremoteDescription" rows="5" cols="40" class="kronolithLongField"></textarea>
  </label>
</div>

<div class="kronolithFormActions">
  <input type="button" value="<?php echo _("Save") ?>" class="kronolithCalendarSave button ok" />
  <input type="button" value="<?php echo _("Delete") ?>" class="kronolithCalendarDelete button ko" />
  <span class="kronolithSeparator"><?php echo _("or") ?></span> <a class="kronolithFormCancel"><?php echo _("Cancel") ?></a>
</div>
</div>

</form>

</div>
