/**
 * Enable auto resizing inside container
 */
let animationFrameID = null
let containerWidth = null

function animationFrameLoop() {
	const container = document.querySelector('#wp-block-glossary-block-glossary')
  const newContainerWidth = container.offsetWidth
	
  if (newContainerWidth !== containerWidth) {
  	handleContainerWidthChanged(container, newContainerWidth)
    containerWidth = newContainerWidth
  }
  
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