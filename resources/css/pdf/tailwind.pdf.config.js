const baseConfig = require('../../../tailwind.config.js');
baseConfig.important = false;
baseConfig.corePlugins = {
    textOpacity: false
};

module.exports = baseConfig;