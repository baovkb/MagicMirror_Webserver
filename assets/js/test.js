function getAllVisibleModule() {
  this.modules = [];
  
  MM.getModules().forEach(module => {
    if (module.name === "EXT-Pages") {
      pagesObj = module.config.pages;
      extPageFixed = module.config.fixed;

      if (!Array.isArray) {
        pageObj = Array.from(pageObj);
      }
      
      pagesObj.for((pageObj) => {

        classStr = pageObj.join(' ') + ' ' + extPageFixed.join(' ');
        classStr = classStr.trim();
        
        tmp = [];
        MM.getModules().forEach(module => {
          let classList = module.data.classes.split(' ');
            classList.forEach(cls => {
              if (classStr.includes(cls)) {
              let name = this.modifyName(module.data.name);				
              tmp.push({
                "name": name,
                "hidden": module.hidden,
                "identifier": module.identifier,
              });
              }
            });
    
        });
        this.modules.push(tmp);
        
      });		
      return;
    }
  });

}