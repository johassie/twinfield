module.exports = function( grunt ) {
	// Project configuration.
	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),

		// PHP Code Sniffer
		phpcs: {
		    application: {
		        dir: ['src/**/*.php']
		    },
		    options: {
		        bin: 'vendor/bin/phpcs',
		        standard: 'psr2'
		    }
		},

		// PHPLint
		phplint: {
			options: {
				phpArgs: {
					'-lf': null
				}
			},
			all: [ 'src/**/*.php' ]
		},

		// PHPUnit
		phpunit: {
			classes: {},
			options: {
				configuration: 'phpunit.xml'
			}
		},
	} );

	grunt.loadNpmTasks( 'grunt-phpcs' );
	grunt.loadNpmTasks( 'grunt-phplint' );
	grunt.loadNpmTasks( 'grunt-phpunit' );

	// Default task(s).
	grunt.registerTask( 'default', [ 'phplint', 'phpcs', 'phpunit' ] );
};
