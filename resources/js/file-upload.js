// Store selected files and track existing files
let previouslySelectedFiles = {};
let existingServerFiles = {};

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('input[type="file"]').forEach(input => {
		const form = input.closest('form');
		const useNativeSubmit = form?.hasAttribute('data-native-submit');

		const inputId = input.id || 'default';
		previouslySelectedFiles[inputId] = [];
		existingServerFiles[inputId] = [];
		
		// Initialize existing files from data attributes if available
		const fileContainer = input.parentElement.querySelector('.file-uploads');
		if (fileContainer) {
			// Look for existing file elements with data-file-id attributes
			fileContainer.querySelectorAll('[data-file-id]').forEach(fileEl => {
				existingServerFiles[inputId].push({
					id: fileEl.dataset.fileId,
					name: fileEl.dataset.fileName || fileEl.title || 'unknown',
					element: fileEl
				});
				
				// Add remove button to existing files
				addRemoveButton(fileEl, inputId);
			});
		}
		
		input.addEventListener('change', handleFileSelect);
		
		// Only intercept submit for forms that don't use native submission
		if (form && !useNativeSubmit) {
			form.addEventListener('submit', (e) => handleFormSubmit(e, input));
		}
	});
});

function addRemoveButton(fileElement, inputId) {
	// Check if button already exists
	if (fileElement.querySelector('.file-remove-btn')) return;
	
	const removeBtn = document.createElement('button');
	removeBtn.className = 'file-remove-btn absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center';
	removeBtn.innerHTML = '×';
	removeBtn.type = 'button';
	
	// Position the container relatively if it's not already
	if (getComputedStyle(fileElement).position === 'static') {
		fileElement.style.position = 'relative';
	}
	
	removeBtn.addEventListener('click', (e) => {
		e.preventDefault();
		
		// If this is a server file, mark it for deletion
		const fileId = fileElement.dataset.fileId;
		if (fileId) {
			existingServerFiles[inputId] = existingServerFiles[inputId].filter(f => f.id !== fileId);
		} else {
			// If this is a new file, remove it from the array
			const fileName = fileElement.title || fileElement.dataset.fileName;
			previouslySelectedFiles[inputId] = previouslySelectedFiles[inputId].filter(
				f => f.name !== fileName
			);
		}
		
		// Remove the element from the DOM
		fileElement.remove();
	});
	
	fileElement.appendChild(removeBtn);
}

/**
 * Handles file selection and preview generation
 * @param {Event} evt - The change event
 */
function handleFileSelect(evt) { 
	const input = evt.target;
	const inputId = input.id || 'default';
	const newFiles = Array.from(input.files);
	const fileUploadsContainer = input.parentElement.querySelector('.file-uploads');
	
	// Clear previous files if not multiple
	if (!input.hasAttribute('multiple')) {
		previouslySelectedFiles[inputId] = [];
		existingServerFiles[inputId] = [];
		if (fileUploadsContainer) {
			fileUploadsContainer.innerHTML = '';
		}
	}
	
	// Store and display new files
	previouslySelectedFiles[inputId] = [...previouslySelectedFiles[inputId], ...newFiles];
	
	if (fileUploadsContainer) {
		newFiles.forEach((file) => {
			const reader = new FileReader();
			reader.onload = (e) => { 
				const fileWrapper = document.createElement('div');
				fileWrapper.className = 'relative inline-block m-1';
				fileWrapper.innerHTML = `<img src="${e.target.result}" title="${file.name}" class="pointer-events-none w-24 h-24 object-cover" />`;
				fileUploadsContainer.appendChild(fileWrapper);
				
				// Add remove button to new file preview
				addRemoveButton(fileWrapper, inputId);
			}; 
			reader.readAsDataURL(file); 
		});
	}
}

/**
 * Handles form submission with file uploads
 * @param {Event} e - The submit event
 * @param {HTMLElement} fileInput - The file input element
 */
function handleFormSubmit(e, fileInput) {
	e.preventDefault();
	
	const form = e.target;
	const inputId = fileInput.id || 'default';
	const method = form.method.toUpperCase();
	
	// Create FormData and add files
	const formData = new FormData(form);
	formData.delete(fileInput.name);
	
	// Add all new files (name already includes [] for multiple inputs)
	previouslySelectedFiles[inputId].forEach(file => {
		formData.append(fileInput.name, file);
	});
	
	// Add list of existing files to keep (those not removed)
	const keepFileIds = existingServerFiles[inputId].map(f => f.id);
	if (keepFileIds.length > 0) {
		formData.append('keep_' + fileInput.name, JSON.stringify(keepFileIds));
	}
	
	// Prepare fetch options
	const fetchOptions = {
		method: method,
		redirect: 'follow'
	};
	
	// Handle GET vs POST/PUT/etc.
	if (method !== 'GET' && method !== 'HEAD') {
		fetchOptions.body = formData;
	} else {
		const url = new URL(form.action);
		for (const [key, value] of formData.entries()) {
			if (!(value instanceof File)) {
				url.searchParams.append(key, value);
			}
		}
		form.action = url.toString();
	}
	
	// Submit and handle response
	fetch(form.action, fetchOptions)
		.then(handleResponse)
		.then(data => {
			if (data && typeof data === 'object') {
				if (data.redirect) {
					window.location.href = data.redirect;
				} else if (data.html) {
					document.open();
					document.write(data.html);
					document.close();
				} else if (!data.redirected) {
					window.location.reload();
				}
			} else {
				window.location.reload();
			}
		})
		.catch(error => {
			// Silent error handling
		});
}

/**
 * Processes the response based on content type
 * @param {Response} response - The fetch response
 * @returns {Promise} - Promise with processed data
 */
function handleResponse(response) {
	if (!response.ok) {
		throw new Error('Network response was not ok');
	}
	
	const contentType = response.headers.get('content-type');
	if (contentType && contentType.includes('application/json')) {
		return response.json();
	} else if (contentType && contentType.includes('text/html')) {
		return response.text().then(html => ({ html }));
	} else {
		return response.text();
	}
}