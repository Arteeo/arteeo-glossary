/**
 * Enable auto resizing inside container
 */
let animationFrameID = null
let containerWidth = [];

function animationFrameLoop() {
  const containers = document.querySelectorAll('.wp-block-glossary-block-glossary');
  
  containers.forEach(
    function(currentValue, currentIndex, listObj) { 
      const newContainerWidth = currentValue.offsetWidth
	
      if (newContainerWidth !== containerWidth[currentIndex]) {
        handleContainerWidthChanged(currentValue, newContainerWidth);
        containerWidth[currentIndex] = newContainerWidth;
      }
    },
    ''
  );
  
  animationFrameID = window.requestAnimationFrame(animationFrameLoop)
}

function handleContainerWidthChanged(container, newContainerWidth) {
	container.classList.toggle('sm', newContainerWidth >= 576)
  container.classList.toggle('md', newContainerWidth >= 768)
  container.classList.toggle('lg', newContainerWidth >= 992)
  container.classList.toggle('xl', newContainerWidth >= 1200)
}

document.addEventListener('DOMContentLoaded', () => {
  animationFrameLoop()
})