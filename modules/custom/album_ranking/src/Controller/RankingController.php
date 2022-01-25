<?php

namespace Drupal\album_ranking\Controller;

use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;


class RankingController
{
  public function page()
  {

    $webform = Webform::load('album_servey');

    if ($webform->hasSubmissions()) {
      $query = \Drupal::entityQuery('webform_submission')->condition('webform_id', 'album_servey');
      $result = $query->execute();

      $submission_data = [];
      foreach ($result as $item) {
        $submission = WebformSubmission::load($item);
        $submission_data[] = $submission->getData()['your_favorite_album'];
      }

      $ranking = array_count_values($submission_data);
      arsort($ranking);

      foreach ($ranking as $key => $value){
        $node = Node::load($key);

        $image = ImageStyle::load('medium')->buildUrl($node->field_image->entity->getFileUri());
        /*$image = file_create_url($node->field_image->entity->getFileUri());*/

        $ranking[$key] = array(
          'vote' => $value,
          'node' => $node,
          'image' => $image
        );
      }
      //dump($ranking);

    }

    return array(
      "#theme" => 'album_ranking',
      "#content" => $ranking,
      "#title" => 'Albums ranking'
    );
  }

}
