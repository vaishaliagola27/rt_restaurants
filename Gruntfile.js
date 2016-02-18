module.exports = function (grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			all: {
				files: [{
						expand: true,
						cwd: 'assets/js/',
						src: ['*.js', '!*.min.js'],
						dest: 'assets/js/',
						ext: '.min.js'
					}]
			}
		},
		jshint: {
			files: ['assets/js/*.js'],
			options: {
				globals: {
					jQuery: true
				},
				"bitwise": true,
				"curly": true,
				"eqeqeq": true,
				"forin": true,
				"latedef": true,
				"maxparams": 3,
				"noarg": true,
				"nonew": true,
				"shadow": true,
				"undef": true,
				"unused": true,
				"browser": true,
			}


		},
		watch: {
			files: ['<%= jshint.files %>'],
			tasks: ['jshint']
		}
	});
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.registerTask('default', ['jshint', 'uglify']);
};