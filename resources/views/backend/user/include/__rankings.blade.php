<div
    class="tab-pane fade"
    id="pills-rankings"
    role="tabpanel"
    aria-labelledby="pills-rankings-tab"
>
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="site-card">
                <div class="site-card-header">
                    <h4 class="title">{{ __('Rankings') }}</h4>
                </div>
                <div class="site-card-body table-responsive">
                    <div class="ranking-list row justify-content-center">
                        @php
                            $alreadyRank = json_decode($user->rankings, true);
                            $current_ranking = $user->ranking_id;
        
                            $rankings = \App\Models\Ranking::where('status', true)->get();
                        @endphp
                        @foreach($rankings as $ranking)
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                                <div class="single-badge @if(!in_array($ranking->id,$alreadyRank)) locked @endif">
                                    <div class="badge">
                                        <div class="img"><img src="{{ asset($ranking->icon) }}" alt=""></div>
                                    </div>
                                    <div class="content">
                                        <h3 class="title">{{ $ranking->ranking_name }}</h3>
                                        <p class="description">{{ $ranking->description }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div>
                        <form action="{{route('admin.user.ranking-update', $user->id)}}" method="post">
                            @method('POST')
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="site-input-groups">
                                        <label class="box-input-label" for="">{{ __('Current Ranking:') }}</label>
                                        <select name="user_ranking" class="form-select" id="user_ranking">
                                            @foreach($rankings as $ranking)
                                                <option value="{{$ranking->id}}"
                                                        @if($user->ranking_id == $ranking->id) selected @endif>{{$ranking->ranking_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit"
                                            class="site-btn-sm primary-btn w-100 centered">{{ __('Save Changes') }}</button>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
