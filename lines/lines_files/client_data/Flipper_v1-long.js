var visLayers = new Array();
var layerOnTop = 0;

// public
function flipLayers(layers) {
	flipNamedLayers("lyr", layers, false);
}

// public
function reorderLayers(layers) {
	flipNamedLayers("lyr", layers, true);
}





// PRIVATE
function zIndexReorder(element, newZIndex) {
	MM_findObj(element).style.zIndex = newZIndex;
}

function flipNamedLayers(prefix, layers, useZOrdering) {

  var cur = visLayers[prefix];
  if (!cur)
  	cur = 0;

	if(useZOrdering) {  
		layerOnTop = layerOnTop +1;
		zIndexReorder(prefix+cur, layerOnTop);
	}else{
		for (i=0; i<layers; i++) {
			MM_showHideLayers(prefix+i, '', 'hide');
		}
	}
 
	var aLayer = MM_findObj(prefix+cur);
	if (aLayer && aLayer.filters && aLayer.filters.revealTrans) {
		aLayer.filters.revealTrans.apply();
		aLayer.filters.revealTrans.play();
	}

	if(!useZOrdering) {
		MM_showHideLayers(prefix+cur, '', 'show');
	}  
  
	visLayers[prefix] = (cur+1)%layers;
}
