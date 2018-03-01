// rollup.config.js
import resolve from 'rollup-plugin-node-resolve';
import babel from 'rollup-plugin-babel';
import uglify from 'rollup-plugin-uglify';
import { minify } from 'uglify-es';

let feInput = './dev-js/';
let feOutput = 'js/';

const fs = require('fs');
let aFeConfiguration = [];
fs.readdirSync(feInput).forEach(file => {
	aFeConfiguration.push(
		{
			input: feInput+file,
			output: {
				file: feOutput+file,
				format: 'iife'
			},
			plugins: [
				resolve(),
				uglify({}, minify),
				babel({
					exclude: 'node_modules/**' // only transpile our source code
				})
			]
		}
	);
});

let beInput = './admin/source/dev-js/';
let beOutput = 'admin/source/js/';

let aBeConfiguration = [];
fs.readdirSync(beInput).forEach(file => {
	aBeConfiguration.push(
		{
			input: beInput+file,
			output: {
				file: beOutput+file,
				format: 'iife'
			},
			plugins: [
				resolve(),
				uglify({}, minify),
				babel({
					exclude: 'node_modules/**' // only transpile our source code
				})
			]
		}
	);
});


// export default aFeConfiguration;
export default aBeConfiguration;
