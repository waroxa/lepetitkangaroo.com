<?php
/**
 * Plugin Update Class For Essential Grid
 * Enables automatic updates on the Plugin
 *
 * @package Essential_Grid_Plugin_Update
 * @author  ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 * @since 1.1.0
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Plugin_Update
{

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @param string $version
	 */
	public function __construct($version)
	{
		$this->set_version($version);
	}

	/**
	 * update the version
	 * 
	 * @param string $new_version
	 */
	public function update_version($new_version)
	{
		Essential_Grid_Db::update_version($new_version);
	}

	/**
	 * set the version in class
	 * 
	 * @param string $new_version
	 */
	public function set_version($new_version)
	{
		$this->version = $new_version;
	}

	/**
	 * update routine, do updates depending on what version we currently are
	 */
	public function do_update_process()
	{
		if (version_compare($this->version, '1', '<=')) {
			$this->update_to_110();
		}

		if (version_compare($this->version, '2.0', '<')) {
			$this->update_to_20();
		}

		if (version_compare($this->version, '2.0.1', '<')) {
			$this->update_to_201();
		}

		if (version_compare($this->version, '2.1.5', '<')) {
			$this->update_to_215();
		}

		if (version_compare($this->version, '2.1.6', '<')) {
			$this->update_to_216();
		}

		if (version_compare($this->version, '2.2', '<')) {
			$this->update_to_22();
		}

		if (version_compare($this->version, '2.3', '<')) {
			$this->update_to_23();
		}

		if (version_compare($this->version, '3.0', '<')) {
			$this->update_to_3();
		}
		
		if (version_compare($this->version, '3.0.13', '<')) {
			$this->update_to_3013();
		}
		
		if (version_compare($this->version, '3.0.14', '<')) {
			$this->update_to_3014();
		}
		
		if (version_compare($this->version, '3.0.16', '<')) {
			$this->update_to_3016();
		}

		if (version_compare($this->version, '3.0.17', '<')) {
			$this->update_to_3017();
		}
		
		if (version_compare($this->version, '3.0.17.1', '<')) {
			$this->update_to_30171();
		}
		
		if (version_compare($this->version, '3.0.18', '<')) {
			$this->update_to_3018();
		}
		
		if (version_compare($this->version, '3.0.19', '<')) {
			$this->update_to_3019();
		}
		
		if (version_compare($this->version, '3.1.0', '<')) {
			$this->update_to_310();
		}
		
		if (version_compare($this->version, '3.1.9', '<')) {
			$this->update_to_319();
			$this->update_version('3.1.9');
			$this->set_version('3.1.9');
		}

		do_action('essgrid_do_update_process', $this->version);
	}

	/**
	 * adds navigation skins to support dropdowns
	 * 
	 * update to 1.1.0
	 * @since: 1.1.0
	 */
	public function update_to_110()
	{
		$navigation_skins = [
			['handle' => 'flat-light', 'css' => '/* FLAT LIGHT SKIN DROP DOWN 1.1.0 */
.flat-light .esg-filterbutton 								{ 	color:#000;color:rgba(0,0,0,0.5);}

.flat-light	.esg-selected-filterbutton						{	background:#fff; padding:10px 20px 10px 30px; color:#000; border-radius: 4px;font-weight:700;}

.flat-light .esg-cartbutton,
.flat-light .esg-cartbutton a,
.flat-light .esg-cartbutton a:visited,
.flat-light .esg-cartbutton a:hover,
.flat-light .esg-cartbutton i,
.flat-light .esg-cartbutton i.before								{	font-weight:700; color:#000; }
.flat-light .esg-selected-filterbutton .eg-icon-down-open	{	 margin-left:5px;font-size:12px; line-height: 20px; vertical-align: top;}

.flat-light .esg-selected-filterbutton:hover .eg-icon-down-open,
.flat-light .esg-selected-filterbutton.hoveredfilter .eg-icon-down-open	{	 color:rgba(0,0,0,1); }

.flat-light .esg-dropdown-wrapper							{	border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;}
.flat-light .esg-dropdown-wrapper .esg-filterbutton			{	line-height: 25px; white-space: nowrap; padding:0px 10px; font-weight:700; text-align: left}
.flat-light .esg-dropdown-wrapper .esg-filter-checked		{	display:inline-block; margin-left:0px !important;margin-right:7px; margin-top:-2px !important; line-height: 15px !important;}
.flat-light .esg-dropdown-wrapper .esg-filter-checked span	{	vertical-align: middle; line-height:20px;}'],
			['handle' => 'flat-dark', 'css' => '/* FLAT DARK SKIN DROP DOWN 1.1.0 */
.flat-dark .esg-filterbutton 								{ 	color:#fff !important}

.flat-dark .esg-selected-filterbutton						{	background: #3A3A3A; background: rgba(0, 0, 0, 0.2); padding:10px 20px 10px 30px; color:#fff; border-radius: 4px;font-weight:600; }

.flat-dark .esg-cartbutton,
.flat-dark .esg-cartbutton a,
.flat-dark .esg-cartbutton a:visited,
.flat-dark .esg-cartbutton a:hover,
.flat-dark .esg-cartbutton i,
.flat-dark .esg-cartbutton i.before						{	font-weight:600; color:#fff; }
.flat-dark .esg-selected-filterbutton .eg-icon-down-open	{	margin-left:5px;font-size:12px; line-height: 20px; vertical-align: top;}

.flat-dark .esg-selected-filterbutton:hover .eg-icon-down-open,
.flat-dark .esg-selected-filterbutton.hoveredfilter .eg-icon-down-open		{	 color:rgba(255,255,255,1); }
.flat-dark .esg-cartbutton:hover,
.flat-dark .esg-selected-filterbutton:hover, 
.flat-dark .esg-selected-filterbutton.hoveredfilter		{	background: rgba(0, 0, 0, 0.5); }

.flat-dark .esg-dropdown-wrapper							{	background:#222; border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;}
.flat-dark .esg-dropdown-wrapper .esg-filterbutton			{	background:transparent !important;line-height: 25px; white-space: nowrap; padding:0px 10px; font-weight:600; text-align: left; color:#fff; color:rgba(255,255,255,0.5) !important;}
.flat-dark .esg-dropdown-wrapper .esg-filterbutton:hover,
.flat-dark .esg-dropdown-wrapper .esg-filterbutton.selected	{	background:transparent !important; color:#fff; color:rgba(255,255,255,1) !important;}
.flat-dark .esg-dropdown-wrapper .esg-filter-checked		{	display:inline-block; margin-left:0px !important;margin-right:7px; margin-top:-2px !important; line-height: 15px !important;}
.flat-dark .esg-dropdown-wrapper .esg-filter-checked span	{	vertical-align: middle; line-height:20px;}'],
			['handle' => 'minimal-dark', 'css' => '/* MINIMAL DARK SKIN DROP DOWN 1.1.0 */
.minimal-dark .esg-filterbutton 								{ 	color:#fff !important}

.minimal-dark .esg-selected-filterbutton						{	background: transparent; border: 1px solid rgba(255, 255, 255, 0.1);background: rgba(0, 0, 0, 0); padding:10px 20px 10px 30px; color:#fff; border-radius: 4px;font-weight:600;}

.minimal-dark .esg-cartbutton									{	border: 1px solid rgba(255, 255, 255, 0.1) !important; border-radius:5px !important; -moz-border-radius:5px !important;-webkit-border-radius:5px !important;}
.minimal-dark .esg-cartbutton,
.minimal-dark .esg-cartbutton a,
.minimal-dark .esg-cartbutton a:visited,
.minimal-dark .esg-cartbutton a:hover,
.minimal-dark .esg-cartbutton i,
.minimal-dark .esg-cartbutton i.before						{	font-weight:600; color:#fff; }
.minimal-dark .esg-selected-filterbutton .eg-icon-down-open	{	margin-left:5px;font-size:12px; line-height: 20px; vertical-align: top; color:#fff;}

.minimal-dark .esg-cartbutton:hover,
.minimal-dark .esg-selected-filterbutton:hover, 
.minimal-dark .esg-selected-filterbutton.hoveredfilter		{	border-color: rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); }

.minimal-dark .esg-dropdown-wrapper								{	background:#333; background:rgba(0,0,0,0.95);border: 1px solid rgba(255, 255, 255, 0.1);border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;}
.minimal-dark .esg-dropdown-wrapper .esg-filterbutton			{	border:none !important; box-shadow:none !important; background:transparent !important;line-height: 25px; white-space: nowrap; padding:0px 10px; font-weight:600; text-align: left; color:#fff; color:rgba(255,255,255,0.5) !important;}
.minimal-dark .esg-dropdown-wrapper .esg-filterbutton:hover,
.minimal-dark .esg-dropdown-wrapper .esg-filterbutton.selected	{	background:transparent !important; color:#fff; color:rgba(255,255,255,1) !important; }
.minimal-dark .esg-dropdown-wrapper .esg-filter-checked			{	display:inline-block; margin-left:0px !important;margin-right:7px; margin-top:-2px !important; line-height: 15px !important; border: 1px solid rgba(255, 255, 255, 0.2)}
.minimal-dark .esg-dropdown-wrapper .esg-filter-checked span	{	vertical-align: middle; line-height:20px;}'],
			['handle' => 'minimal-light', 'css' => '/* MINIMAL LIGHT SKIN DROP DOWN 1.1.0 */
.minimal-light .esg-filterbutton 								{ 	color:#999 !important}

.minimal-light .esg-selected-filterbutton						{	 border: 1px solid #E5E5E5;background: #fff; padding:10px 20px 10px 30px; color:#999; border-radius: 4px;font-weight:700;  }

.minimal-light .esg-selected-filterbutton .eg-icon-down-open	{	margin-left:5px;font-size:12px; line-height: 20px; vertical-align: top; color:#999;}

.minimal-light .esg-filter-wrapper .esg-filterbutton span i 			{ color: #fff !important;  }
.minimal-light .esg-filter-wrapper .esg-filterbutton:hover span, 
.minimal-light .esg-filter-wrapper .esg-filterbutton.selected span		{ color: #000 !important;  }
.minimal-light .esg-filter-wrapper .esg-filterbutton:hover span i, 
.minimal-light .esg-filter-wrapper .esg-filterbutton.selected span i		{ color: #fff !important;  }

.minimal-light .esg-selected-filterbutton:hover .eg-icon-down-open,
.minimal-light .esg-selected-filterbutton.hoveredfilter .eg-icon-down-open		{	 color:rgba(0,0,0,1) !important; }
.minimal-light .esg-cartbutton:hover, 							
.minimal-light .esg-selected-filterbutton:hover, 
.minimal-light .esg-selected-filterbutton.hoveredfilter		{	border-color: #bbb; color: #333; box-shadow: 0px 3px 5px 0px rgba(0,0,0,0.13); }

.minimal-light .esg-dropdown-wrapper							{	background:#fff; border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px; border: 1px solid #bbb; box-shadow: 0px 3px 5px 0px rgba(0,0,0,0.13);}
.minimal-light .esg-dropdown-wrapper .esg-filterbutton			{	border:none !important;line-height: 25px; white-space: nowrap; padding:0px 10px; font-weight:700; text-align: left; color:#999; }
.minimal-light .esg-dropdown-wrapper .esg-filterbutton:hover,
.minimal-light .esg-dropdown-wrapper .esg-filterbutton.selected	{	background:transparent !important; color:#000 !important; box-shadow: none !important}
.minimal-light .esg-dropdown-wrapper .esg-filter-checked		{	display:inline-block; margin-left:0px !important;margin-right:7px; margin-top:-2px !important; line-height: 15px !important;}
.minimal-light .esg-dropdown-wrapper .esg-filter-checked span	{	vertical-align: middle; line-height:20px;}'],
			['handle' => 'simple-light', 'css' => '/* SIMPLE LIGHT SKIN DROP DOWN 1.1.0 */
.simple-light .esg-filterbutton 								{ 	color:#999 !important}

.simple-light .esg-selected-filterbutton						{	 border: 1px solid #E5E5E5;background: #eee; padding:5px 5px 5px 10px; color:#000; font-weight:400;}

.simple-light .esg-selected-filterbutton .eg-icon-down-open		{	margin-left:5px;font-size:9px; line-height: 20px; vertical-align: top; color:#000;}

.simple-light .esg-cartbutton:hover,
.simple-light .esg-selected-filterbutton:hover, 
.simple-light .esg-selected-filterbutton.hoveredfilter		{	background-color: #fff; border-color: #bbb; color: #333; box-shadow: 0px 3px 5px 0px rgba(0,0,0,0.13); }

.simple-light .esg-filter-wrapper .esg-filterbutton span		{ color: #000;  }
.simple-light .esg-filter-wrapper .esg-filterbutton:hover span, 
.simple-light .esg-filter-wrapper .esg-filterbutton.selected span		{ color: #000 !important;  }
.simple-light .esg-filter-wrapper .esg-filterbutton:hover span i, 
.simple-light .esg-filter-wrapper .esg-filterbutton.selected span i		{ color: #fff !important;  }

.simple-light .esg-dropdown-wrapper								{	background:#fff; border: 1px solid #bbb; box-shadow: 0px 3px 5px 0px rgba(0,0,0,0.13);}
.simple-light .esg-dropdown-wrapper .esg-filterbutton			{	border:none !important;background:transparent !important;line-height: 25px; white-space: nowrap; padding:0px 10px; font-weight:400; text-align: left; }
.simple-light .esg-dropdown-wrapper .esg-filterbutton span { color:#777; }
.simple-light .esg-dropdown-wrapper .esg-filterbutton:hover,
.simple-light .esg-dropdown-wrapper .esg-filterbutton.selected	{	color:#000 !important; box-shadow: none !important}
.simple-light .esg-dropdown-wrapper .esg-filter-checked			{	display:inline-block; margin-left:0px !important;margin-right:7px; margin-top:-2px !important; line-height: 15px !important;}
.simple-light .esg-dropdown-wrapper .esg-filter-checked span	{	vertical-align: middle; line-height:20px;}'],
			['handle' => 'simple-dark', 'css' => '/* SIMPLE DARK SKIN DROP DOWN */
.simple-dark .esg-filterbutton 									{ 	color:#fff !important}

.simple-dark .esg-selected-filterbutton							{	 border: 1px solid rgba(255, 255, 255, 0.15);background:rgba(255, 255, 255, 0.08);padding:5px 5px 5px 10px; color:#fff; font-weight:600;}

.simple-dark .esg-cartbutton									{	border: 1px solid rgba(255, 255, 255, 0.1) !important; }
.simple-dark .esg-cartbutton,
.simple-dark .esg-cartbutton a,
.simple-dark .esg-cartbutton a:visited,
.simple-dark .esg-cartbutton i,
.simple-dark .esg-cartbutton i.before						{	font-weight:600; color:#fff; }

.simple-dark .esg-cartbutton:hover a, 
.simple-dark .esg-cartbutton:hover i 							{ color: #000; }

.simple-dark .esg-selected-filterbutton:hover .eg-icon-down-open,
.simple-dark .esg-selected-filterbutton.hoveredfilter .eg-icon-down-open		{	 color:#000; }
.simple-dark .esg-cartbutton:hover, 							
.simple-dark .esg-selected-filterbutton:hover, 
.simple-dark .esg-selected-filterbutton.hoveredfilter			{	border-color: #fff; color: #000; box-shadow: 0px 3px 5px 0px rgba(0,0,0,0.13); background: #fff; }

.simple-dark .esg-selected-filterbutton .eg-icon-down-open		{	margin-left:5px;font-size:9px; line-height: 20px; vertical-align: top; color:#fff;}

.simple-dark .esg-filter-wrapper .esg-filterbutton:hover span, 
.simple-dark .esg-filter-wrapper .esg-filterbutton.selected span		{ color: #000 !important;  }

.simple-dark .esg-dropdown-wrapper								{	background:#fff; border: 1px solid #bbb; box-shadow: 0px 3px 5px 0px rgba(0,0,0,0.13); }

.simple-dark .esg-dropdown-wrapper .esg-filterbutton			{	border:none !important;background:transparent !important;line-height: 25px; white-space: nowrap; padding:0px 10px; font-weight:600; text-align: left; color:#777 !important; }
.simple-light .esg-dropdown-wrapper .esg-filterbutton span { color:#777; }
.simple-dark .esg-dropdown-wrapper .esg-filterbutton:hover,
.simple-dark .esg-dropdown-wrapper .esg-filterbutton.selected	{	color:#000 !important; box-shadow: none !important; font-weight: 600;}
.simple-dark .esg-dropdown-wrapper .esg-filter-checked			{	display:inline-block; margin-left:0px !important;margin-right:7px; margin-top:-2px !important; line-height: 15px !important; border: 1px solid #444;}
.simple-dark .esg-dropdown-wrapper .esg-filter-checked span		{	vertical-align: middle; line-height:20px;}'],
			['handle' => 'text-dark', 'css' => '/* TEXT DARK SKIN DROP DOWN 1.1.0 */
.text-dark .esg-filterbutton 									{ 	color: #FFF;color: rgba(255, 255, 255, 0.4) !important}
	
.text-dark .esg-selected-filterbutton							{	padding:5px 5px 5px 10px; color: #FFF;color: rgba(255, 255, 255, 0.4);  font-weight:600;}

.text-dark .esg-cartbutton										{	 }
.text-dark .esg-cartbutton,
.text-dark .esg-cartbutton a,
.text-dark .esg-cartbutton a:visited,
.text-dark .esg-cartbutton a:hover,
.text-dark .esg-cartbutton i,
.text-dark .esg-cartbutton i.before							{	font-weight:600; color: #FFF; color: rgba(255, 255, 255, 0.4); }

.text-dark .esg-cartbutton:hover a, 
.text-dark .esg-cartbutton:hover i 								{ color: rgba(255, 255, 255, 1); }

.text-dark .esg-selected-filterbutton:hover .eg-icon-down-open,
.text-dark .esg-selected-filterbutton.hoveredfilter .eg-icon-down-open		{	 color: rgba(255, 255, 255, 1); }
.text-dark .esg-cartbutton:hover, 							
.text-dark .esg-selected-filterbutton:hover, 
.text-dark .esg-selected-filterbutton.hoveredfilter				{	color: rgba(255, 255, 255, 1); }

.text-dark .esg-selected-filterbutton .eg-icon-down-open		{	margin-left:5px;font-size:9px; line-height: 20px; vertical-align: top; color: #FFF;color: rgba(255, 255, 255, 0.4); }

.text-dark .esg-filter-wrapper .esg-filterbutton:hover span, 
.text-dark .esg-filter-wrapper .esg-filterbutton.selected span	{ color: rgba(255, 255, 255, 1);  }

.text-dark .esg-dropdown-wrapper								{	border: 1px solid rgba(0, 0, 0, 0.15); background:#000; background:rgba(0, 0, 0, 0.95); }
.text-dark .esg-dropdown-wrapper .esg-filterbutton				{	border:none !important;background:transparent !important;line-height: 25px; white-space: nowrap; padding:0px 10px; font-weight:600; text-align: left; color:#999 !important; }
.text-dark .esg-dropdown-wrapper .esg-filterbutton span  		{   text-decoration: none !important; }
.text-dark .esg-dropdown-wrapper .esg-filterbutton:hover,
.text-dark .esg-dropdown-wrapper .esg-filterbutton.selected		{	color:#fff !important; box-shadow: none !important; }
.text-dark .esg-dropdown-wrapper .esg-filter-checked			{	display:inline-block; margin-left:0px !important;margin-right:7px; margin-top:-2px !important; line-height: 15px !important; border: 1px solid #444;}
.text-dark .esg-dropdown-wrapper .esg-filterbutton.selected .esg-filter-checked,
.text-dark .esg-dropdown-wrapper .esg-filterbutton:hover .esg-filter-checked	{	color:#fff;}

.text-dark .esg-dropdown-wrapper .esg-filter-checked span		{	vertical-align: middle; line-height:20px; color:#000;}'],
			['handle' => 'text-light', 'css' => '/* TEXT LIGHT SKIN DROP DOWN 1.1.0 */
.text-light .esg-filterbutton 									{ 	color: #999}

.text-light .esg-selected-filterbutton							{	padding:5px 5px 5px 10px; color: #999; font-weight:600;}

.text-light .esg-cartbutton										{	 }
.text-light .esg-cartbutton,
.text-light .esg-cartbutton a,
.text-light .esg-cartbutton a:visited,
.text-light .esg-cartbutton a:hover,
.text-light .esg-cartbutton i,
.text-light .esg-cartbutton i.before							{	font-weight:600; color: #999; }

.text-light .esg-cartbutton:hover a, 
.text-light .esg-cartbutton:hover i 							{ color: #444; }

.text-light .esg-selected-filterbutton:hover .eg-icon-down-open,
.text-light .esg-selected-filterbutton.hoveredfilter .eg-icon-down-open		{	 color: #444; }
.text-light .esg-cartbutton:hover, 							
.text-light .esg-selected-filterbutton:hover, 
.text-light .esg-selected-filterbutton.hoveredfilter			{	color: #444; }

.text-light .esg-selected-filterbutton .eg-icon-down-open		{	margin-left:5px;font-size:9px; line-height: 20px; vertical-align: top; color: #999; }

.text-light .esg-filter-wrapper .esg-filterbutton:hover span, 
.text-light .esg-filter-wrapper .esg-filterbutton.selected span	{ text-decoration: none !important; }

.text-light .esg-dropdown-wrapper								{	border: 1px solid rgba(255, 255, 255, 0.15); background:#fff; background:rgba(255, 255, 255, 0.95); }
.text-light .esg-dropdown-wrapper .esg-filterbutton				{	border:none !important;background:transparent !important;line-height: 25px; white-space: nowrap; padding:0px 10px; font-weight:600; text-align: left; color:#999 !important; }
.text-light .esg-dropdown-wrapper .esg-filterbutton span  		{   text-decoration: none !important; }
.text-light .esg-dropdown-wrapper .esg-filterbutton:hover,
.text-light .esg-dropdown-wrapper .esg-filterbutton.selected	{	color:#000 !important; box-shadow: none !important; }
.text-light .esg-dropdown-wrapper .esg-filter-checked			{	display:inline-block; margin-left:0px !important;margin-right:7px; margin-top:-2px !important; line-height: 15px !important; border: 1px solid #ddd;}
.text-light .esg-dropdown-wrapper .esg-filterbutton.selected .esg-filter-checked,
.text-light .esg-dropdown-wrapper .esg-filterbutton:hover .esg-filter-checked	{	color:#000;}

.text-light .esg-dropdown-wrapper .esg-filter-checked span		{	vertical-align: middle; line-height:20px; color:#000;}']
		];

		foreach ($navigation_skins as $skin) {
			$old_skin = Essential_Grid_Db::get_entity('nav_skins')->get_by_handle( $skin['handle'] );
			if ($old_skin !== false) {
				$old_skin['css'] .= "\n\n\n" . $skin['css'];
				//modify variables to meet requirement for update function
				$old_skin['skin_css'] = $old_skin['css'];
				$old_skin['sid'] = $old_skin['id'];
				unset($old_skin['name']);
				unset($old_skin['css']);
				unset($old_skin['id']);
				Essential_Grid_Navigation::update_create_navigation_skin_css($old_skin);
			}
		}
		$this->update_version('1.1.0');
		$this->set_version('1.1.0');
	}

	/**
	 * adds navigation skins to support search
	 * 
	 * update to 2.0
	 * @since: 2.0
	 */
	public function update_to_20()
	{
		$navigation_skins = [
			['handle' => 'flat-light', 'css' => '/* FLAT LIGHT SEARCH 2.0 */
.flat-light input.eg-search-input[type="text"]{	background: #FFF !important;padding: 0px 15px !important;
												color: #000 !important;border-radius: 5px;-moz-border-radius: 5px;-webkit-border-radius: 5px;line-height: 40px !important;border: none !important;box-shadow: none !important;
												font-size: 12px !important;text-transform: uppercase;font-weight: 700;
											}
.flat-light input.eg-search-input[type="text"]::-webkit-input-placeholder { color:#000 !important}
.flat-light input.eg-search-input[type="text"]:-moz-placeholder { color:#000 !important}
.flat-light input.eg-search-input[type="text"]::-moz-placeholder { color:#000 !important}
.flat-light input.eg-search-input[type="text"]:-ms-input-placeholder	{ color:#000 !important}
.flat-light .eg-search-submit,
.flat-light .eg-search-clean  { background:#fff; color:#999; width:40px;height:40px; text-align: center; vertical-align: top; border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;margin-left:5px;}
.flat-light .eg-search-submit:hover,
.flat-light .eg-search-clean:hover { color:#000;}'],
			['handle' => 'flat-dark', 'css' => '/* FLAT DARK SEARCH 2.0 */
.flat-dark input.eg-search-input[type="text"]{	background: #3A3A3A !important; background: rgba(0, 0, 0, 0.2) !important;border-radius: 5px;-moz-border-radius: 5px;-webkit-border-radius: 5px;line-height: 40px !important;border: none !important;box-shadow: none !important;
												font-size: 12px !important;text-transform: uppercase;
												padding: 0px 15px !important;color: #fff !important;
											}
.flat-dark input.eg-search-input[type="text"]::-webkit-input-placeholder { color:#fff !important}
.flat-dark input.eg-search-input[type="text"]:-moz-placeholder {	color:#fff !important}
.flat-dark input.eg-search-input[type="text"]::-moz-placeholder {	color:#fff !important}
.flat-dark input.eg-search-input[type="text"]:-ms-input-placeholder {	color:#fff !important}

.flat-dark input.eg-search-input[type="text"]:hover,
.flat-dark input.eg-search-input[type="text"]:focus { background: #4A4A4A !important;background: rgba(0, 0, 0, 0.5) !important;}
.flat-dark .eg-search-submit,
.flat-dark .eg-search-clean	{	background: #3A3A3A !important; background: rgba(0, 0, 0, 0.2) !important;
								color:#fff; width:40px;height:40px; text-align: center; vertical-align: top; border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;margin-left:5px;
							}
.flat-dark .eg-search-submit:hover,
.flat-dark .eg-search-clean:hover { background: #4A4A4A !important;background: rgba(0, 0, 0, 0.5) !important;color:#fff;}'],
			['handle' => 'minimal-dark', 'css' => '/* MINIMAL DARK SEARCH 2.0 */
.minimal-dark input.eg-search-input[type="text"] { background: transparent !important; background: rgba(0, 0, 0, 0) !important;
													padding: 0px 15px !important;color: #fff !important;line-height: 38px !important;
													border-radius: 5px 0px 0px 5px;-moz-border-radius: 5px 0px 0px 5px;-webkit-border-radius: 5px 0px 0px 5px;														
													border:1px solid #fff !important;border:1px solid rgba(255,255,255,0.1) !important;
													border-right: none !important;box-shadow: none !important;
													font-size: 12px !important;font-weight: 600;
												}
												
.minimal-dark input.eg-search-input[type="text"]::-webkit-input-placeholder { color:#fff !important}
.minimal-dark input.eg-search-input[type="text"]:-moz-placeholder { color:#fff !important}
.minimal-dark input.eg-search-input[type="text"]::-moz-placeholder { color:#fff !important}
.minimal-dark input.eg-search-input[type="text"]:-ms-input-placeholder { color:#fff !important}

.minimal-dark input.eg-search-input[type="text"]:hover,
.minimal-dark input.eg-search-input[type="text"]:focus { background: transparent !important;background: rgba(255, 255, 255, 0.1) !important;border-color: rgba(255, 255, 255, 0.2) !important;box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.13) !important;}
.minimal-dark .eg-search-submit,
.minimal-dark .eg-search-clean { background: transparent !important; background: rgba(0, 0, 0, 0) !important;color:#fff; width:40px;height:40px; text-align: center; vertical-align: top; 
								border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;margin-left:0px;
								border:1px solid #fff !important;border:1px solid rgba(255,255,255,0.1) !important;
							}
.minimal-dark .eg-search-submit { border-left:none !important; border-right:none !important; border-radius:0;-webkit-border-radius:0;-moz-border-radius:0;}
.minimal-dark .eg-search-clean { border-left:none !important;  border-radius:0px 5px 5px 0px; -webkit-border-radius:0px 5px 5px 0px; -moz-border-radius:0px 5px 5px 0px}
.minimal-dark .eg-search-submit:hover,
.minimal-dark .eg-search-clean:hover { background: transparent !important;background: rgba(255, 255, 255, 0.1) !important;border-color: rgba(255, 255, 255, 0.2) !important;box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.13) !important;}'],
			['handle' => 'minimal-light', 'css' => '/* MINIMAL LIGHT SEARCH 2.0 */
.minimal-light input.eg-search-input[type="text"] {	background: #fff !important;
													padding: 0px 15px !important;color: #999 !important;line-height: 38px !important;
													border-radius: 5px 0px 0px 5px;-moz-border-radius: 5px 0px 0px 5px;-webkit-border-radius: 5px 0px 0px 5px;
													border:1px solid #E5E5E5 !important;
													border-right: none !important;box-shadow: none !important;
													font-size: 12px !important;font-weight: 600;
												}
												
.minimal-light input.eg-search-input[type="text"]::-webkit-input-placeholder { color:#999 !important}
.minimal-light input.eg-search-input[type="text"]:-moz-placeholder { color:#999 !important}
.minimal-light input.eg-search-input[type="text"]::-moz-placeholder { color:#999 !important}
.minimal-light input.eg-search-input[type="text"]:-ms-input-placeholder { color:#999 !important}

.minimal-light input.eg-search-input[type="text"]:hover,
.minimal-light input.eg-search-input[type="text"]:focus { background: #fff !important;border-color: #bbb !important;box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.13) !important;}
.minimal-light .eg-search-submit,
.minimal-light .eg-search-clean { background:#fff !important;color:#999; width:40px;height:40px; text-align: center; vertical-align: top; 
									border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;margin-left:0px;
									border:1px solid #E5E5E5 !important;
								}
.minimal-light .eg-search-submit { border-right:none !important; border-radius:0; -webkit-border-radius:0; -moz-border-radius:0;}
.minimal-light .eg-search-clean { border-radius:0px 5px 5px 0px; -webkit-border-radius:0px 5px 5px 0px; -moz-border-radius:0px 5px 5px 0px}
.minimal-light .eg-search-submit:hover,
.minimal-light .eg-search-clean:hover { background: #fff !important; border-color: #bbb !important; box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.13) !important;}'],
			['handle' => 'simple-light', 'css' => '/* SIMPLE LIGHT SEARCH 2.0 */
.simple-light .eg-search-wrapper { line-height: 30px !important}
.simple-light input.eg-search-input[type="text"] { background: #eee !important; padding: 0px 15px !important;
												border: 1px solid #E5E5E5 !important;
												color: #000 !important; line-height: 30px !important; box-shadow: none !important;
												font-size: 12px !important; text-transform: uppercase; font-weight: 400;
												}
.simple-light input.eg-search-input[type="text"]:hover,
.simple-light input.eg-search-input[type="text"]:focus { background-color: #fff !important}
.simple-light input.eg-search-input[type="text"]::-webkit-input-placeholder { color:#000 !important}
.simple-light input.eg-search-input[type="text"]:-moz-placeholder { color:#000 !important}
.simple-light input.eg-search-input[type="text"]::-moz-placeholder { color:#000 !important}
.simple-light input.eg-search-input[type="text"]:-ms-input-placeholder { color:#000 !important}
.simple-light .eg-search-submit,
.simple-light .eg-search-clean { border: 1px solid #E5E5E5 !important; background:#eee; color:#000; width:32px; height:32px; text-align: center; font-size:14px; 
								vertical-align: top; margin-left:5px;
							  }
.simple-light .eg-search-submit:hover,
.simple-light .eg-search-clean:hover { color:#000; background:#fff !important}'],
			['handle' => 'simple-dark', 'css' => '/* SIMPLE DARK SEARCH 2.0 */
.simple-dark .eg-search-wrapper { line-height: 30px !important}
.simple-dark input.eg-search-input[type="text"] { background: rgba(255, 255, 255, 0.08) !important; padding: 0px 15px !important;
												border:1px solid rgba(255, 255, 255, 0.15) !important;
												color: #fff !important; line-height: 30px !important; box-shadow: none !important;
												font-size: 12px !important; font-weight: 600;
											  }
.simple-dark input.eg-search-input[type="text"]:hover,
.simple-dark input.eg-search-input[type="text"]:focus { background-color: #fff !important; color:#000 !important;}
.simple-dark input.eg-search-input[type="text"]::-webkit-input-placeholder { color:#fff !important}
.simple-dark input.eg-search-input[type="text"]:-moz-placeholder { color:#fff !important}
.simple-dark input.eg-search-input[type="text"]::-moz-placeholder { color:#fff !important}
.simple-dark input.eg-search-input[type="text"]:-ms-input-placeholder { color:#fff !important}
.simple-dark input:hover.eg-search-input[type="text"]::-webkit-input-placeholder { color:#000 !important}
.simple-dark input:hover.eg-search-input[type="text"]:-moz-placeholder { color:#000 !important}
.simple-dark input:hover.eg-search-input[type="text"]::-moz-placeholder { color:#000 !important}
.simple-dark input:hover.eg-search-input[type="text"]:-ms-input-placeholder { color:#000 !important}

.simple-dark .eg-search-submit,
.simple-dark .eg-search-clean { border: 1px solid rgba(255, 255, 255, 0.15) !important; background: rgba(255, 255, 255, 0.08); 
								color:#fff; width:32px; height:32px; text-align: center; font-size:12px; 
								vertical-align: top;margin-left:5px;
							 }
.simple-dark .eg-search-submit:hover,
.simple-dark .eg-search-clean:hover{ color:#000; background:#fff !important}'],
			['handle' => 'text-dark', 'css' => '/* TEXT DARK SEARCH 2.0 */
.text-dark .eg-search-wrapper {	line-height: 32px !important; vertical-align: middle !important}
.text-dark input.eg-search-input[type="text"] { background: transparent !important; padding: 0px 15px !important;
												border:none !important; margin-bottom:0px !important;
												color: #fff !important; color: rgba(255, 255, 255, 0.4) !important; line-height: 20px !important; box-shadow: none !important;
												font-size: 12px !important; font-weight: 600;
											}
.text-dark input.eg-search-input[type="text"]:hover,
.text-dark input.eg-search-input[type="text"]:focus {	 color:#fff !important;}
.text-dark input.eg-search-input[type="text"]::-webkit-input-placeholder { color:#fff !important;color: rgba(255, 255, 255, 0.4) !important;}
.text-dark input.eg-search-input[type="text"]:-moz-placeholder { color:#fff !important; color: rgba(255, 255, 255, 0.4) !important;}
.text-dark input.eg-search-input[type="text"]::-moz-placeholder { color:#fff !important; color: rgba(255, 255, 255, 0.4) !important;}
.text-dark input.eg-search-input[type="text"]:-ms-input-placeholder { color:#fff !important; color: rgba(255, 255, 255, 0.4) !important;}
.text-dark input:hover.eg-search-input[type="text"]::-webkit-input-placeholder { color:#fff !important}
.text-dark input:hover.eg-search-input[type="text"]:-moz-placeholder { color:#fff !important}
.text-dark input:hover.eg-search-input[type="text"]::-moz-placeholder { color:#fff !important}
.text-dark input:hover.eg-search-input[type="text"]:-ms-input-placeholder { color:#fff !important}


.text-dark .eg-search-submit,
.text-dark .eg-search-clean { border: none !important; background: transparent; line-height:20px;vertical-align: middle;
								color:#fff;color: rgba(255, 255, 255, 0.4) !important;height:20px; text-align: center; font-size:12px; 
								margin-left:10px; padding-left:10px; border-left:1px solid #fff !important; border-left:1px solid rgba(255, 255, 255, 0.2) !important;
							}
.text-dark .eg-search-submit:hover,
.text-dark .eg-search-clean:hover{ color:#fff !important;}'],
			['handle' => 'text-light', 'css' => '/* TEXT LIGHT SEARCH 2.0 */
.text-light .eg-search-wrapper { line-height: 32px !important; vertical-align: middle !important}
.text-light input.eg-search-input[type="text"] { background: transparent !important; padding: 0px 15px !important;
												border:none !important; margin-bottom:0px !important;
												color: #999 !important; line-height: 20px !important; box-shadow: none !important;
												font-size: 12px !important;font-weight: 600;
											}
.text-light input.eg-search-input[type="text"]:hover,
.text-light input.eg-search-input[type="text"]:focus	{ color:#444 !important;}
.text-light input.eg-search-input[type="text"]::-webkit-input-placeholder { color:#999 !important;}
.text-light input.eg-search-input[type="text"]:-moz-placeholder { color:#999 !important;}
.text-light input.eg-search-input[type="text"]::-moz-placeholder { color:#999 !important;}
.text-light input.eg-search-input[type="text"]:-ms-input-placeholder { color:#999 !important;}
.text-light input:hover.eg-search-input[type="text"]::-webkit-input-placeholder {	color:#444 !important}
.text-light input:hover.eg-search-input[type="text"]:-moz-placeholder { color:#444 !important}
.text-light input:hover.eg-search-input[type="text"]::-moz-placeholder { color:#444 !important}
.text-light input:hover.eg-search-input[type="text"]:-ms-input-placeholder { color:#444 !important}

.text-light .eg-search-submit,
.text-light .eg-search-clean { border: none !important; background: transparent; line-height:20px; vertical-align: middle;
								color:#999;height:20px; text-align: center; font-size:12px; 
								margin-left:10px; padding-left:10px; border-left:1px solid #e5e5e5 !important; 
							}
.text-light .eg-search-submit:hover,
.text-light .eg-search-clean:hover { color:#444 !important; }']
		];

		foreach ($navigation_skins as $skin) {
			$old_skin = Essential_Grid_Db::get_entity('nav_skins')->get_by_handle( $skin['handle'] );
			if ($old_skin !== false) {
				$old_skin['css'] .= "\n\n\n" . $skin['css'];
				//modify variables to meet requirement for update function
				$old_skin['skin_css'] = $old_skin['css'];
				$old_skin['sid'] = $old_skin['id'];
				unset($old_skin['name']);
				unset($old_skin['css']);
				unset($old_skin['id']);
				Essential_Grid_Navigation::update_create_navigation_skin_css($old_skin);
			}
		}
		$this->update_version('2.0');
		$this->set_version('2.0');
	}

	/**
	 * adds navigation skins to support search further, fixing some missing styles
	 * 
	 * update to 2.0.1
	 * @since: 2.0.1
	 */
	public function update_to_201()
	{
		$navigation_skins = [
			['handle' => 'simple-light', 'css' => '/* SIMPLE LIGHT SEARCH 2.0.1 */
.simple-light input.eg-search-input[type="text"] {
	border-radius: 0px !important;
	height: 32px;
	box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
}

.simple-light .eg-search-submit, .simple-light .eg-search-clean {
	width:32px;height:32px;
	box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
}'],
			['handle' => 'minimal-dark', 'css' => '/* MINIMAL DARK SEARCH 2.0.1 */
.minimal-dark input.eg-search-input[type="text"] {
	height: 40px;
	box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
}
.minimal-dark .eg-search-submit, .minimal-dark .eg-search-clean {
	box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
}'],
			['handle' => 'minimal-light', 'css' => '/* MINIMAL LIGHT SEARCH 2.0.1 */
.minimal-light .eg-search-submit, .minimal-light .eg-search-clean {
	box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
}'],
			['handle' => 'simple-dark', 'css' => '/* SIMPLE DARK SEARCH 2.0.1 */
.simple-dark input.eg-search-input[type="text"] { box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	height: 34px;
	border-radius: 0px !important;
}']];

		foreach ($navigation_skins as $skin) {
			$old_skin = Essential_Grid_Db::get_entity('nav_skins')->get_by_handle( $skin['handle'] );
			if ($old_skin !== false) {
				$old_skin['css'] .= "\n\n\n" . $skin['css'];
				//modify variables to meet requirement for update function
				$old_skin['skin_css'] = $old_skin['css'];
				$old_skin['sid'] = $old_skin['id'];
				unset($old_skin['name']);
				unset($old_skin['css']);
				unset($old_skin['id']);
				Essential_Grid_Navigation::update_create_navigation_skin_css($old_skin);
			}
		}
		$this->update_version('2.0.1');
		$this->set_version('2.0.1');
	}

	/**
	 * update process
	 * @since: 2.1.5
	 * @does: adds new param(s) to all previous Item Skins
	 */
	private function addDefaultSkinParam($skins, $params)
	{
		if (!empty($skins)) {
			foreach ($skins as $skin) {
				if (!empty($skin['layers'])) {
					$layers = $skin['layers'];
					foreach ($layers as $prop => $layer) {
						if (!empty($layer['settings'])) {
							$settings = $layer['settings'];
							foreach ($params as $key => $val) $settings[$key] = $val;
							$layer['settings'] = $settings;
							$layers[$prop] = $layer;
						}
					}
					$skin['layers'] = wp_json_encode($layers);
					Essential_Grid_Item_Skin::update_save_item_skin($skin);
				}
			}
		}
	}

	/**
	 * update to 2.1.5
	 * @since: 2.1.5
	 * @does: adds new layer param(s) to all previous Item Skins via the "addDefaultSkinParam" function above
	 */
	public function update_to_215()
	{
		// new item skin layer params
		$paramsToPush = ['source-catmax' => '-1'];

		//Item Skins
		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			global $wpdb;

			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$skins = Essential_Grid_Item_Skin::get_essential_item_skins();
				$this->addDefaultSkinParam($skins, $paramsToPush);
				// 2.2.5
				restore_current_blog();
			}
		} else {
			$skins = Essential_Grid_Item_Skin::get_essential_item_skins();
			$this->addDefaultSkinParam($skins, $paramsToPush);
		}

		$this->update_version('2.1.5');
		$this->set_version('2.1.5');
	}

	/**
	 * covering all the bases
	 * 
	 * @since: 2.1.6 
	 * @param mixed $val
	 * @return bool
	 */
	/*  */
	private static function checkFalsey($val)
	{
		return !empty($val) && $val !== 'NULL' && $val !== 'false' && $val !== 'undefined';
	}

	/**
	 * upgrades previous and imported skins with new options 
	 * 
	 * @since: 2.1.6
	 * @param array $skin
	 * @param bool $canConvert
	 * @param array $toConvert
	 * @param bool $fromImport
	 * @return mixed
	 */
	private static function process_skin_216($skin, $canConvert, $toConvert, $fromImport = false)
	{
		if (isset($skin['layers']) && !empty($skin['params'])) {
			$params = $skin['params'];
			$layers = $skin['layers'];

			// decode if not already decoded
			if (is_string($params)) $params = json_decode($params, true);
			if (is_string($layers) && !empty($layers)) $layers = json_decode($layers, true);

			// one more check for params, as they are required for any given skin
			if (empty($params)) {
				if ($fromImport) return $skin;
				else return null;
			}

			$paramsChanged = false;
			$layersChanged = false;

			/* join color and opacity for params */
			if ($canConvert) {

				$colors = $toConvert['settings'];
				foreach ($colors as $colorSet) {

					$colorProp = $colorSet['color'];
					$opacityProp = $colorSet['opacity'];

					if (isset($params[$colorProp]) && isset($params[$opacityProp])) {

						$color = $params[$colorProp];
						$opacity = $params[$opacityProp];

						if (static::checkFalsey($color) && is_numeric($opacity) && $opacity != '100') {

							$converted = ESGColorpicker::convert($color, $opacity);
							if (!empty($converted)) {
								$params[$colorProp] = $converted;
								$params[$opacityProp] = '100';
								$paramsChanged = true;
							}
						}
					}
				}
			}

			if (!empty($layers)) {
				$colors = $toConvert['layers'];
				$invisible = isset($params['cover-group-animation']) && $params['cover-group-animation'] === 'none';

				foreach ($layers as $key => $layer) {

					if (!empty($layer['settings'])) {
						$layerSets = $layer['settings'];
						$toUpdate = false;

						/* set new visibility options */
						if (!empty($layerSets['transition']) && !isset($layerSets['always-visible-desktop']) && !isset($layerSets['always-visible-mobile'])) {
							$hidden = $layerSets['transition'] === 'none' && $invisible ? 'true' : '';
							$layer['settings']['always-visible-desktop'] = $hidden;
							$layer['settings']['always-visible-mobile'] = $hidden;
							$toUpdate = true;
						}

						/* join color and opacity for layers */
						if ($canConvert) {
							foreach ($colors as $colorSet) {
								$colorProp = $colorSet['color'];
								$opacityProp = $colorSet['opacity'];
								if (isset($layerSets[$colorProp]) && isset($layerSets[$opacityProp])) {
									$color = $layerSets[$colorProp];
									$opacity = $layerSets[$opacityProp];
									if (static::checkFalsey($color) && is_numeric($opacity) && $opacity != '100') {
										$converted = ESGColorpicker::convert($color, $opacity);
										if (!empty($converted)) {
											$layer['settings'][$colorProp] = $converted;
											$layer['settings'][$opacityProp] = '100';
											$toUpdate = true;
										}
									}
								}
							}
						}
						if ($toUpdate) {
							$layers[$key] = $layer;
							$layersChanged = true;
						}
					}
				}
			}

			if (!$fromImport) {
				if ($paramsChanged || $layersChanged) {
					if ($paramsChanged) $skin['params'] = $params;
					if ($layersChanged) $skin['layers'] = wp_json_encode($layers);
					Essential_Grid_Item_Skin::update_save_item_skin($skin);
				}
			} else {
				$skin['params'] = wp_json_encode($params);
				$skin['layers'] = wp_json_encode($layers);
				return $skin;
			}
		} else if ($fromImport) {
			return $skin;
		}
		
		return null;
	}

	/*
	  2.1.6
	  Determining if a Layer was officially set to "always visible" and setting the new "showWithoutHover" options accordingly
	  Also merges all colors+opacity where applicable
	*/
	public static function process_update_216($skins, $fromImport = false)
	{
		if (!empty($skins)) {
			// vars defined here so they are only created once
			$canConvert = class_exists('ESGColorpicker');
			$toConvert = [
				'settings' => [
					['color' => 'container-background-color', 'opacity' => 'element-container-background-color-opacity'],
					['color' => 'content-shadow-color', 'opacity' => 'content-shadow-alpha']
				],
				'layers' => [
					['color' => 'background-color', 'opacity' => 'bg-alpha'],
					['color' => 'background-color-hover', 'opacity' => 'bg-alpha-hover'],
					['color' => 'shadow-color', 'opacity' => 'shadow-alpha'],
					['color' => 'shadow-color-hover', 'opacity' => 'shadow-alpha-hover']
				]
			];

			// update cycle
			if (!$fromImport) {
				foreach ($skins as $skin) {
					static::process_skin_216($skin, $canConvert, $toConvert);
				}
			} // import cycle
			else {
				return static::process_skin_216($skins, $canConvert, $toConvert, true);
			}
		} else if ($fromImport) {
			return $skins;
		}
		
		return null;
	}

	/**
	 * update to 2.1.6
	 * @since: 2.1.6
	 * @does: adds new "showWithoutHover" options and upgrade to new Color Picker
	 */
	public function update_to_216()
	{
		//Item Skins
		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			global $wpdb;

			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$skins = Essential_Grid_Item_Skin::get_essential_item_skins();
				$this->process_update_216($skins);
				// 2.2.5
				restore_current_blog();
			}
		} else {
			$skins = Essential_Grid_Item_Skin::get_essential_item_skins();
			$this->process_update_216($skins);
		}

		$this->update_version('2.1.6');
		$this->set_version('2.1.6');
	}

	/**
	 * update to 2.1.7
	 * @since: 2.1.7
	 * @does: adds new "post likes votes" options
	 */
	public function update_to_22()
	{
		foreach (get_posts() as $post) {
			if (!is_numeric(get_post_meta($post->ID, 'eg_votes_count', true))) {
				update_post_meta($post->ID, 'eg_votes_count', 0);
			}
		}

		// Global Styles
		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			global $wpdb;

			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$css = Essential_Grid_Global_Css::get_global_css_styles();
				// new
				$css = str_replace('.esg-entry-media img', '.esg-media-poster', $css);
				Essential_Grid_Global_Css::set_global_css_styles(apply_filters('essgrid_propagate_default_global_css_multisite_update_to_220', $css, $site->blog_id));
				// 2.2.5
				restore_current_blog();
			}
		} else {
			$css = Essential_Grid_Global_Css::get_global_css_styles();
			// new
			$css = str_replace('.esg-entry-media img', '.esg-media-poster', $css);
			Essential_Grid_Global_Css::set_global_css_styles(apply_filters('essgrid_propagate_default_global_css_update_to_220', $css));
		}

		$this->update_version('2.2');
		$this->set_version('2.2');
	}

	/**
	 * update to 2.3
	 * @since: 2.3
	 * @does: adds a new skin to the exisiting installation
	 */
	public function insert_skin($skin)
	{
		$skins = Essential_Grid_Item_Skin::get_essential_item_skins();
		if (!empty($skins)) {
			foreach ($skins as $skn) {
				if (isset($skn['handle']) && $skn['handle'] === 'esgblankskin') return;
			}
		}

		Essential_Grid_Db::get_entity('skins')->insert( [ 'name' => $skin['name'], 'handle' => $skin['handle'], 'params' => $skin['params'], 'layers' => $skin['layers'] ] );
	}

	/**
	 * update to 2.3
	 * @since: 2.3
	 * @does: adds new blank skin for custom grid blank items
	 */
	public function update_to_23()
	{
		global $wpdb;

		$blank_skin = ['name' => 'ESGBlankSkin', 'handle' => 'esgblankskin', 'params' => '{"eg-item-skin-element-last-id":"0","choose-layout":"even","show-content":"none","content-align":"left","image-repeat":"no-repeat","image-fit":"cover","image-align-horizontal":"center","image-align-vertical":"center","element-x-ratio":"4","element-y-ratio":"3","splitted-item":"none","cover-type":"full","container-background-color":"rgba(0, 0, 0, 0)","cover-always-visible-desktop":"false","cover-always-visible-mobile":"false","cover-background-size":"cover","cover-background-repeat":"no-repeat","cover-background-image":"0","cover-background-image-url":"","full-bg-color":"rgba(255, 255, 255, 0)","full-padding":["0","0","0","0"],"full-border":["0","0","0","0"],"full-border-radius":["0","0","0","0"],"full-border-color":"transparent","full-border-style":"none","full-overflow-hidden":"false","content-bg-color":"rgba(255, 255, 255, 0)","content-padding":["0","0","0","0"],"content-border":["0","0","0","0"],"content-border-radius":["0","0","0","0"],"content-border-color":"transparent","content-border-style":"none","all-shadow-used":"none","content-shadow-color":"#000000","content-box-shadow":["0","0","0","0"],"cover-animation-top-type":"","cover-animation-delay-top":"0","cover-animation-top":"fade","cover-animation-center-type":"","cover-animation-delay-center":"0","cover-animation-center":"none","cover-animation-bottom-type":"","cover-animation-delay-bottom":"0","cover-animation-bottom":"fade","cover-group-animation":"none","media-animation":"none","media-animation-delay":"0","element-hover-image":"false","hover-image-animation":"fade","hover-image-animation-delay":"0","link-set-to":"none","link-link-type":"none","link-url-link":"","link-meta-link":"","link-javascript-link":"","link-target":"_self"}', 'layers' => "[]", 'settings' => null];
		$new_skin = ['name' => $blank_skin['name'], 'handle' => $blank_skin['handle'], 'params' => $blank_skin['params'], 'layers' => $blank_skin['layers']];

		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site

			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$this->insert_skin($new_skin);
				restore_current_blog();
			}
		} else {
			$this->insert_skin($new_skin);
		}
		$this->update_version('2.3');
		$this->set_version('2.3');
	}

	/**
	 * update to 3.0.0
	 * @since: 3.0.0
	 * @does: adds new default navigation skins
	 */
	public function update_to_3()
	{
		Essential_Grid_Navigation::propagate_default_navigation_skins();
		$this->update_version('3.0');
		$this->set_version('3.0');
	}
	
	/**
	 * update to 3.0.13
	 * @since: 3.0.13
	 * @does: update navigation skins
	 */
	public function update_to_3013()
	{
		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			global $wpdb;

			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$this->_update_skins_3013();
				restore_current_blog();
			}
		} else {
			$this->_update_skins_3013();
		}
		
		$this->update_version('3.0.13');
		$this->set_version('3.0.13');
	}
	
	protected function _update_skins_3013()
	{
		$tmpl_find = '.#handle# .esg-filterbutton:last-child';
		$tmpl_replace = '.#handle# .esg-filterbutton-last';
		$tmpl_add = '
.#handle# .esg-pagination-button:last-child { 
	border-right: none; 
}';
		
		$styles = ['light', 'dark'];
		foreach ($styles as $style) {
			$handle = 'text-'.$style;
			$skin = Essential_Grid_Db::get_entity('nav_skins')->get_by_handle( $handle );
			if (!empty($skin)) {
				$find_style = str_replace('#handle#', $handle, $tmpl_find);
				$replace_style = str_replace('#handle#', $handle, $tmpl_replace);
				$add_style = str_replace('#handle#', $handle, $tmpl_add);
				$data = [
					'sid' => $skin['id'],
					'skin_css' => str_replace($find_style, $replace_style, $skin['css']) . $add_style,
				];
				Essential_Grid_Navigation::update_create_navigation_skin_css($data);
			}
		}
	}

	/**
	 * update to 3.0.14
	 * @since: 3.0.14
	 * @does: update navigation skins
	 */
	public function update_to_3014()
	{
		global $wpdb;
		
		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$this->_update_skins_3014();
				$this->_update_nav_skins_3014();
				$this->_update_grids_3014();
				restore_current_blog();
			}
		} else {
			$this->_update_skins_3014();
			$this->_update_nav_skins_3014();
			$this->_update_grids_3014();
		}

		$this->update_version('3.0.14');
		$this->set_version('3.0.14');
	}
	
	protected function _update_grids_3014()
	{
		global $wpdb;
		
		$grids = Essential_Grid_Db::get_entity('grids')->get_grids(false, false);
		foreach ($grids as $grid) {
			$grid->params = self::update_grid_desktop_xl($grid->params);
			Essential_Grid_Db::get_entity('grids')->update( [ 'params' => wp_json_encode($grid->params) ], $grid->id );
		}
	}

	/**
	 * move @import to be the first rule in nav skins css ( Imported rules must precede all other types of rule )
	 */
	protected function _update_skins_3014()
	{
		$skins = ['pat-lafontaine', 'grant-fuhr', 'leon-draisaitl', 'uwe-krupp', 'clark-gillies', 'rod-langway', 'ray-bourque'];
		$skins_data = [
			'pat-lafontaine' => [
				'remove' => '@import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,400;0,500;0,600;1,400&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");',
				'find' => '*************************************/',
				'replace' => '*************************************/
@import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,400;0,500;0,600;1,400&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");
',
			], 
			'grant-fuhr' => [
				'remove' => '@import url("https://fonts.googleapis.com/icon?family=Material+Icons");',
				'find' => '*************************************/',
				'replace' => '*************************************/
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");
',
			],
			'leon-draisaitl' => [
				'remove' => '@import url("https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");',
				'find' => '*************************************/',
				'replace' => '*************************************/
@import url("https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");
',
			], 
			'uwe-krupp' => [
				'remove' => '@import url("https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");',
				'find' => '*************************************/',
				'replace' => '*************************************/
@import url("https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");
',
			], 
			'clark-gillies' => [
				'remove' => '@import url("https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");',
				'find' => '*************************************/',
				'replace' => '*************************************/
@import url("https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");
',
			], 
			'rod-langway' => [
				'remove' => '@import url("https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;700&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");',
				'find' => '*************************************/',
				'replace' => '*************************************/
@import url("https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;700&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");
',
			], 
			'ray-bourque' => [
				'remove' => '@import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");',
				'find' => '*************************************/',
				'replace' => '*************************************/
@import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");
',
			],
		];
		foreach ($skins as $handle) {
			$skin = Essential_Grid_Db::get_entity('nav_skins')->get_by_handle( $handle );
			if (empty($skin)) continue;
			
			//remove 
			$skin['css'] = str_replace($skins_data[$handle]['remove'], '', $skin['css']);
			//replace
			$skin['css'] = str_replace($skins_data[$handle]['find'], $skins_data[$handle]['replace'], $skin['css']);
			
			//update
			Essential_Grid_Navigation::update_create_navigation_skin_css(['sid' => $skin['id'], 'skin_css' => $skin['css']]);
		}
	}

	protected function _update_nav_skins_3014()
	{
		$handle = 'flat-light';
		$add_style = '

.flat-light .esg-navigationbutton.esg-loadmore {
	margin: 15px;
	padding: 0 15px;
	width: auto;
}';
		$skin = Essential_Grid_Db::get_entity('nav_skins')->get_by_handle( $handle );
		if (!empty($skin)) {
			$data = [
				'sid' => $skin['id'],
				'skin_css' => $skin['css'] . $add_style,
			];
			Essential_Grid_Navigation::update_create_navigation_skin_css($data);
		}
	}

	/**
	 * modify grid data to reflect new desktop XL dimension
	 * 
	 * @param array $params
	 * @return array
	 */
	public static function update_grid_desktop_xl($params)
	{
		$devices = Essential_Grid_Base::get_basic_devices();
		$amount = count($devices);
		$to_update = ['columns','columns-width','columns-height','mascontent-height'];
		
		//add advanced rows to list of params to update
		foreach ($params as $k => $v) {
			if (strpos($k, 'columns-advanced-rows-') !== false) $to_update[] = $k;
		}
		
		//check if any param needs an update
		$need_update = false;
		foreach ($to_update as $key) {
			if (isset($params[$key]) && is_array($params[$key]) && count($params[$key]) < $amount) {
				$need_update = true;
				break;
			}
		}
		if (!$need_update) return $params;
		
		//duplicate first item till we get enough params
		foreach ($to_update as $key) {
			if (!isset($params[$key]) || !is_array($params[$key])) continue;
			while (count($params[$key]) < $amount) {
				array_unshift($params[$key], $params[$key][0]);
			}
		}
		
		//update width manually
		if (isset($params['columns-width'][0]) && $params['columns-width'][0] < $devices[0]['width']) {
			$params['columns-width'][0] = $devices[0]['width'];
		}
		
		return $params;
	}

	/**
	 * update to 3.0.16
	 * @since: 3.0.16
	 * @does: check if addon functionality needed
	 */
	public function update_to_3016()
	{
		global $wpdb;

		$upgrade = new Essential_Grid_Update(ESG_REVISION);
		$upgrade->force = true;

		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$upgrade->_retrieve_version_info();
				$this->_update_3016_watermarks();
				$this->_update_3016_mediafilters();
				restore_current_blog();
			}
		} else {
			$upgrade->_retrieve_version_info();
			$this->_update_3016_watermarks();
			$this->_update_3016_mediafilters();
		}

		$this->update_version('3.0.16');
		$this->set_version('3.0.16');
	}

	/**
	 * check if watermarks is enabled and used by grids 
	 * update wp options to support watermarks as addon
	 * update grid parameters to support watermarks as addon
	 * 
	 * @return void
	 */
	protected function _update_3016_watermarks()
	{
		$handle = 'esg-watermarks-addon';
		$original_option = 'tp_eg_watermarks';
		$options = get_option($original_option, []);
		delete_option($original_option);
		
		if (
			empty($options) ||
			!is_array($options) ||
			!isset($options['watermarks-enabled']) ||
			$options['watermarks-enabled'] !== 'true'
		) {
			return;
		}
		
		//get grids and check watermarks status
		$arrGrids = Essential_Grid_Db::get_entity('grids')->get_grids(false, false);
		foreach ($arrGrids as $grid) {
			$grid = (array)$grid;
			if (!isset($grid['params']['watermarks-enabled']) || $grid['params']['watermarks-enabled'] !== 'true') continue;
			
			//status now stored in addons param
			unset($grid['params']['watermarks-enabled']);
			
			if (!isset($grid['params']['addons']) || !is_array($grid['params']['addons'])) {
				$grid['params']['addons'] = [];
			}
			$grid['params']['addons'][$handle] = true;
		
			Essential_Grid_Admin::update_create_grid($grid);
		}

		unset($options['watermarks-enabled']);
		update_option($handle . '_options', $options);
	}

	/**
	 * check if mediafilters is enabled and used by grids
	 * update wp options to support mediafilters as addon
	 * update grid parameters to support mediafilters as addon
	 *
	 * @return void
	 */
	protected function _update_3016_mediafilters()
	{
		$handle = 'esg-mediafilters-addon';
		$original_option = 'tp_eg_enable_media_filter';
		$option = get_option($original_option, 'false');
		delete_option($original_option);

		if (empty($option) || "false" === $option) {
			return;
		}

		//get grids and check mediafilters status
		$arrGrids = Essential_Grid_Db::get_entity('grids')->get_grids(false, false);
		foreach ($arrGrids as $grid) {
			$grid = (array)$grid;
			if (!isset($grid['postparams']['media-filter-type']) || $grid['postparams']['media-filter-type'] === 'none') continue;

			//option now stored in addons param
			$grid['params']['media-filter-type'] = $grid['postparams']['media-filter-type'];
			unset($grid['postparams']['media-filter-type']);

			if (!isset($grid['params']['addons']) || !is_array($grid['params']['addons'])) {
				$grid['params']['addons'] = [];
			}
			$grid['params']['addons'][$handle] = true;

			Essential_Grid_Admin::update_create_grid($grid);
		}
	}

	/**
	 * update to 3.0.17
	 */
	public function update_to_3017()
	{
		global $wpdb;

		$upgrade = new Essential_Grid_Update(ESG_REVISION);
		$upgrade->force = true;

		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$upgrade->_retrieve_version_info();
				$this->_update_3017_grid_by_source_type('esg-rml-addon', 'rml');
				$this->_update_3017_grid_by_source_type('esg-nextgen-addon', 'nextgen');
				$this->_update_3017_grid_by_source_type('esg-socialmedia-addon', 'stream', ['behance', 'facebook', 'flickr', 'instagram', 'twitter']);
				$this->_update_3017_grid_by_source_type('esg-videoplaylists-addon', 'stream', ['vimeo', 'youtube']);
				restore_current_blog();
			}
		} else {
			$upgrade->_retrieve_version_info();
			$this->_update_3017_grid_by_source_type('esg-rml-addon', 'rml');
			$this->_update_3017_grid_by_source_type('esg-nextgen-addon', 'nextgen');
			$this->_update_3017_grid_by_source_type('esg-socialmedia-addon', 'stream', ['behance', 'facebook', 'flickr', 'instagram', 'twitter']);
			$this->_update_3017_grid_by_source_type('esg-videoplaylists-addon', 'stream', ['vimeo', 'youtube']);
		}
		
		$this->update_version('3.0.17');
		$this->set_version('3.0.17');
	}

	/**
	 * check if $source_type is used as source type by grids
	 * update grid parameters to support $handle as addon
	 *
	 * @param string $handle  addon handle
	 * @param string $source_type
	 * @return void
	 */
	protected function _update_3017_grid_by_source_type($handle, $source_type, $stream_source_type = [])
	{
		$arrGrids = Essential_Grid_Db::get_entity('grids')->get_grids(false, false);
		foreach ($arrGrids as $grid) {
			$grid = (array)$grid;
			if ($grid['postparams']['source-type'] != $source_type) continue;
			if ($grid['postparams']['source-type'] == 'stream' && !in_array($grid['postparams']['stream-source-type'], $stream_source_type)) continue;

			//add $handle to actived addons for this grid
			if (!isset($grid['params']['addons']) || !is_array($grid['params']['addons'])) {
				$grid['params']['addons'] = [];
			}
			$grid['params']['addons'][$handle] = true;

			Essential_Grid_Admin::update_create_grid($grid);
		}
	}

	/**
	 * update to 3.0.17.1
	 */
	public function update_to_30171()
	{
		global $wpdb;

		$upgrade = new Essential_Grid_Update(ESG_REVISION);
		$upgrade->force = true;

		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$upgrade->_retrieve_version_info();
				restore_current_blog();
			}
		} else {
			$upgrade->_retrieve_version_info();
		}

		$this->update_version('3.0.17.1');
		$this->set_version('3.0.17.1');
	}
	
	/**
	 * update to 3.0.18
	 */
	public function update_to_3018()
	{
		global $wpdb;

		$upgrade = new Essential_Grid_Update(ESG_REVISION);
		$upgrade->force = true;

		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$upgrade->_retrieve_version_info();
				restore_current_blog();
			}
		} else {
			$upgrade->_retrieve_version_info();
		}

		$this->update_version('3.0.18');
		$this->set_version('3.0.18');
	}
	
	/**
	 * update to 3.0.19
	 */
	public function update_to_3019()
	{
		global $wpdb;

		$upgrade = new Essential_Grid_Update(ESG_REVISION);
		$upgrade->force = true;

		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$upgrade->_retrieve_version_info();
				restore_current_blog();
			}
		} else {
			$upgrade->_retrieve_version_info();
		}

		$this->update_version('3.0.19');
		$this->set_version('3.0.19');
	}

	/**
	 * add grid bg to settings based on first post or layer
	 *
	 * @return void
	 */
	protected function _update_310_grid_bg()
	{
		$arrGrids = Essential_Grid_Db::get_entity('grids')->get_grids(false, false);
		foreach ($arrGrids as $grid) {
			$grid = (array)$grid;
			if ('custom' == $grid['postparams']['source-type'] || 'post' == $grid['postparams']['source-type'])
				Essential_Grid_Admin::update_create_grid($grid);
		}
	}

	/**
	 * update to 3.1.0
	 */
	public function update_to_310()
	{
		global $wpdb;

		$upgrade = new Essential_Grid_Update(ESG_REVISION);
		$upgrade->force = true;

		if (function_exists('is_multisite') && is_multisite()) {
			//do for each existing site
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$upgrade->_retrieve_version_info();
				$this->_update_310_grid_bg();
				restore_current_blog();
			}
		} else {
			$upgrade->_retrieve_version_info();
			$this->_update_310_grid_bg();
		}

		$this->update_version('3.1.0');
		$this->set_version('3.1.0');
	}

	/**
	 * update to 3.1.9
	 */
	public function update_to_319()
	{
		delete_option('tp_eg_enable_log');
	}
	
}
