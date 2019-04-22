#!/usr/bin/env node

var fs = require('fs');
var input = require('./fetch-cl-args').arg;
var minifiedJsFiles = input.quiz ? 'includes/vendor/wp-pro-quiz/js/min/' : 'assets/js/min/';
var jsFolder = input.quiz ? 'includes/vendor/wp-pro-quiz/js/' : 'assets/js/';

fs.readdir(minifiedJsFiles, function(error, files) {
	if (error) {
		throw error;
	}

	files.forEach( function(file) {
		renamedFile = file.replace('.js', '.min.js');
		fs.rename(minifiedJsFiles + file, jsFolder + renamedFile, function(error){
			if (error) { 
				throw error;
			}
		});

		console.log('\x1b[32m%s\x1b[0m', 'Finished ' + jsFolder + renamedFile);
	});
});
