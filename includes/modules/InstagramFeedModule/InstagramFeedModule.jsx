// External Dependencies
import React, { Component } from 'react';
import './style.css';

class InstagramFeedModule extends Component {

  static slug = 'et_pb_df_instagram_feed';

  render() {
    return (
		<div dangerouslySetInnerHTML={{ __html : this.props.content }} />
    );
  }
}

export default InstagramFeedModule;
