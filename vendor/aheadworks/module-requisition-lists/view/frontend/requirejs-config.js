var config = {
    map: {
        '*': {
            requisitionListManagement: 'Aheadworks_RequisitionLists/js/view/requisition-list/management',
            requisitionListAddTo: 'Aheadworks_RequisitionLists/js/ui/components/requisition-list/add-to'
        }
    },
    config: {
        mixins: {
            'Magento_MultipleWishlist/js/multiple-wishlist': {
                'Aheadworks_RequisitionLists/js/multiple-wishlist-mixin': true
            }
        }
    }
};