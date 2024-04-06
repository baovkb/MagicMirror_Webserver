<div class="container">
    <div class="grid wide">
        <div class="row">
            <div class="col l-2 m-4 c-6">
                <label class="item" for="mm-checkbox">
                    <span class="item-header">Điều chỉnh hiển thị gương</span>
                    <div class="item-content">
                        Magic Mirror²
                    </div>
                </label>
            </div>
            <div class="col l-2 m-4 c-6">
                <label class="item" for="devices-checkbox">
                    <span class="item-header">Điều khiển thiết bị</span>
                    <div class="item-content">
                        <i class="fa-solid fa-fan"></i>
                        <i class="fa-regular fa-lightbulb"></i>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <input type="checkbox" class="mm-checkbox" id="mm-checkbox">
    <div class="modal modal-mm">
        <div class="modal__overlay"></div>
        <div class="modal__body"> 
            <div class="control-list-header">
                <div class="list-heading">
                    Điều chỉnh hiển thị gương
                </div>
                <label class="list-exit" for="mm-checkbox">
                    <i class="fa-regular fa-circle-xmark"></i>
                </label>
            </div>
            <ul class="control-list" id="modules-list">
                
            </ul>
        </div>
    </div>
    
    <input type="checkbox" class="devices-checkbox" id="devices-checkbox">
    <div class="modal modal-devices">
        <div class="modal__overlay"></div>
        <div class="modal__body"> 
            <div class="control-list-header">
                <div class="list-heading">
                    Điều khiển thiết bị
                </div>
                <label class="list-exit" for="devices-checkbox">
                    <i class="fa-regular fa-circle-xmark"></i>
                </label>
            </div>
            <div class="control-list control-list-devices">
                <div class="grid grid-devices">
                    <div class="row" id="devices-list">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?=BASE_URL .'/assets/js/main.js'?>"></script>