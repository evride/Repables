<select class="LicenseSelect" name="data[License][type]">
	<option <?php echo $this->fetch("cc-selected"); ?> value="cc">Attribution - Creative Commons</option>
	<option <?php echo $this->fetch("cc-sa-selected"); ?> value="cc-sa" selected="selected">Attribution - Share Alike - Creative Commons</option>
	<option <?php echo $this->fetch("cc-nd-selected"); ?> value="cc-nd">Attribution - No Derivatives - Creative Commons</option>
	<option <?php echo $this->fetch("cc-nc-selected"); ?> value="cc-nc">Attribution - Non-Commercial - Creative Commons</option>
	<option <?php echo $this->fetch("cc-nc-sa-selected"); ?> value="cc-nc-sa">Attribution - Non-Commercial - Share Alike</option>
	<option <?php echo $this->fetch("cc-nc-nd-selected"); ?> value="cc-nc-nd">Attribution - Non-Commercial - No Derivatives</option>
	<option <?php echo $this->fetch("pd-selected"); ?> value="pd">Creative Commons - Public Domain Dedication</option>
	<option <?php echo $this->fetch("gpl-selected"); ?> value="gpl">Creative Commons - GNU GPL</option>
	<option <?php echo $this->fetch("lgpl-selected"); ?> value="lgpl">Creative Commons - LGPL</option>
	<option <?php echo $this->fetch("bsd-selected"); ?> value="bsd">BSD License</option>
	<option <?php echo $this->fetch("nokia-selected"); ?> value="nokia">Nokia</option>
	<option <?php echo $this->fetch("public-selected"); ?> value="public">Public Domain</option>
</select>