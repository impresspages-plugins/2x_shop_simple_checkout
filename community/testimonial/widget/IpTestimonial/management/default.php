<!-- 
This is widget management template. 
Here you can add management tools.
They will be loaded when user press to edit your widget.

But this widget has no management tools. So lets display the same as in preview.
-->
<?php echo \Ip\View::create('../preview/default.php', $this->getData())->render() ?>