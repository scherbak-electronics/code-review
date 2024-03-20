If don’t you want to use it at the Customer Edit and Customer Registration Page then you need to remove customer_account_edit and customer_account_create (Line 75)
Instead of [‘adminhtml_customer’, ‘customer_account_edit’, ‘customer_account_create’] it will be: [‘adminhtml_customer’]

In another case if you want to use customer attribute at the Customer Edit and Customer Registration Page then you will need to rewrite .phtml templates for these forms at the:
vendor/magento/module-customer/view/frontend/templates/form/edit.phtml
and
vendor/magento/module-customer/view/frontend/templates/form/register.phtml

To achieve this you need to make folder structure at your theme app/design/frontend/{theme_company}/{theme_name}/Magento_Customer/templates/form/
and copy files above there. After this, you can add fields similar way as Magento have in the default template.
The input field name will be our custom attribute code.

<!-- Add Below code in app/design/frontend/{theme_company}/{theme_name}/Magento_Customer/templates/form/register.phtml file. -->

<?php $hdyhau_enable = $this->helper('Zenon\CustomerAttribute\Helper\Data')->getConfig('hdyhau_tab/general/hdyhau_enable');?>
<?php if($hdyhau_enable == 1):?>
    <div class="field hdyhau required" id="hdyhau">
        <label class="label" for="howDidYouHearAboutUsOther"><span><?php echo __('How did you hear about us?') ?></span></label>
        <div class="control">
            <?php
            //$selOptions = $this->helper('Zenon\CustomerAttribute\Helper\Data')->getConfig('hdyhau_tab/general/hdyhau_tab_field');
            $selOptions = $this->helper('Zenon\CustomerAttribute\Helper\Data')->getConfig('hdyhau_tab/general/hdyhau_tab_field_table');
            $selOptions = unserialize($selOptions);
            ?>
            <?php /*if($selOptions != ""):*/?>
            <?php if(count($selOptions) != 0):?>
                <?php //$selOption = explode(",",$selOptions);?>
                <select id="hdyhausel" name="hdyhausel" title="<?php echo __('State/Province') ?>" class="hdyhau_select">
                    <option value=""><?php echo __('Choose...') ?></option>
                    <?php foreach ($selOptions as $option):?>
                        <option value="<?php echo $option['optiontitle'];?>"><?php echo $option['optiontitle'];?></option>
                    <?php endforeach;?>
                    <option value="other"><?php echo __('Other') ?></option>
                </select>
            <?php else:?>
                <select id="hdyhausel" name="hdyhausel" title="<?php echo __('State/Province') ?>" class="hdyhau_select">
                    <option value=""><?php echo __('Choose...') ?></option>
                    <option value="<?php echo __('Google or other search') ?>"><?php echo __('Google or other search') ?></option>
                    <option value="<?php echo __('Word of mouth') ?>"><?php echo __('Word of mouth') ?></option>
                    <option value="<?php echo __('Press') ?>"><?php echo __('Press') ?></option>
                    <option value="<?php echo __('Advertisement') ?>"><?php echo __('Advertisement') ?></option>
                    <option value="<?php echo __('Article or blog post') ?>"><?php echo __('Article or blog post') ?></option>
                    <option value="<?php echo __('Social media') ?>"><?php echo __('Social media') ?></option>
                    <option value="<?php echo __('Email/Newsletter') ?>"><?php echo __('Email/Newsletter') ?></option>
                    <option value="<?php echo __('Family or Friend') ?>"><?php echo __('Family or Friend') ?></option>
                    <option value="<?php echo __('Magazine Article') ?>"><?php echo __('Magazine Article') ?></option>
                    <option value="<?php echo __('Newspaper Story') ?>"><?php echo __('Newspaper Story') ?></option>
                    <option value="<?php echo __('TV/Cable News') ?>"><?php echo __('TV/Cable News') ?></option>
                    <option value="<?php echo __('Website/Search Engine') ?>"><?php echo __('Website/Search Engine') ?></option>
                    <option value="<?php echo __('YouTube') ?>"><?php echo __('YouTube') ?></option>
                    <option value="<?php echo __('Radio') ?>"><?php echo __('Radio') ?></option>
                    <option value="<?php echo __('Print') ?>"><?php echo __('Print') ?></option>
                    <option value="<?php echo __('Outdoor') ?>"><?php echo __('Outdoor') ?></option>
                    <option value="<?php echo __('Online Ads') ?>"><?php echo __('Online Ads') ?></option>
                    <option value="other"><?php echo __('Other') ?></option>
                </select>
            <?php endif;?>

            <input type="text" id="howDidYouHearAboutUsOther" name="howDidYouHearAboutUsOther" title="<?php echo __('How did you hear about us?') ?>" class="input-text hdyhau_input" data-validate="{required:true}" />
            <script type="text/javascript">
                require(['jquery'],function () {
                    jQuery(document).ready(function () {
                        jQuery("#hdyhau").addClass('hideInput');
                        jQuery("#hdyhausel").change(function () {
                            var selOptVal = jQuery('option:selected', this).val();
                            var selOptText = jQuery('option:selected', this).text();
                            if(selOptVal != ""){
                                if(selOptVal != 'other'){
                                    jQuery("#howDidYouHearAboutUsOther").val(selOptText);
                                    jQuery("#hdyhau").addClass('hideInput');
                                }else{
                                    jQuery("#howDidYouHearAboutUsOther").val('');
                                    jQuery("#hdyhau").removeClass('hideInput');
                                }
                            }else{
                                jQuery("#howDidYouHearAboutUsOther").val('');
                                jQuery("#hdyhau").addClass('hideInput');
                            }
                        });
                    });
                });
            </script>
        </div>
    </div>
    <style type="text/css">
        .field.hdyhau {}
        .field.hdyhau .control {}
        .field.hdyhau .control .hdyhau_select {}
        .field.hdyhau .control .hdyhau_input { margin-top: 10px;}
        .field.hdyhau.hideInput .control .hdyhau_input { position: absolute; visibility: hidden; z-index: -1;}
    </style>
<?php endif;?>