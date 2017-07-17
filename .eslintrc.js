module.exports = {
  parser: 'babel-eslint',
  extends: ['airbnb'],
  plugins: ['react'],
  rules: {
    semi: 0,
    'react/display-name': 0,
    'react/jsx-wrap-multilines': 2,
    'react/jsx-filename-extension': [1, { extensions: ['.js', '.jsx'] }],
    'import/no-extraneous-dependencies': [
      'error',
      {
        devDependencies: true,
        optionalDependencies: false,
        peerDependencies: false
      }
    ],
    'comma-dangle': 0,
    'brace-style': ['error', 'stroustrup'],
    'import/no-unresolved': [0],
    'import/extensions': [1, 'never']
  },
  env: {
    node: true,
    mocha: true,
    browser: true
  }
};
