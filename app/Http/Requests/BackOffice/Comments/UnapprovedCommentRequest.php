<?php

namespace App\Http\Requests\BackOffice\Comments;

use App\Models\BackOffice\Comments\UnapprovedComment as model;

class UnapprovedCommentRequest extends CommentRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->defaultAuthorize(model::class);
    }

    /******************** Action rules *********************/
    //
    /******************** Action rules END *********************/
}
