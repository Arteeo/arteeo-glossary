// require modules
const fs = require('fs');
const archiver = require('archiver');


// create a file to stream archive data to.
var output = fs.createWriteStream('./arteeo-glossary.zip');
var archive = archiver('zip', {
  zlib: { level: 9 } // Sets the compression level.
});
 
output.on('close', function() {
  console.log(archive.pointer() + ' total bytes');
  console.log('archiver has been finalized and the output file descriptor has closed.');
});
 
output.on('end', function() {
  console.log('Data has been drained');
});
 
// good practice to catch warnings (ie stat failures and other non-blocking errors)
archive.on('warning', function(err) {
  if (err.code === 'ENOENT') {
    // log warning
  } else {
    // throw error
    throw err;
  }
});
 
// good practice to catch this error explicitly
archive.on('error', function(err) {
  throw err;
});
 
// pipe archive data to the file
archive.pipe(output);
 
// append files from a sub-directory, putting its contents at the root of archive
archive.directory('dist/', false);

// append files from a sub-directory and naming it `new-subdir` within the archive
archive.directory('languages/', 'languages');

archive.finalize();