<div class="btn-filter-show-all d-flex hidden">
    <div class="row mt-2 mb-2">
        <div class="col-12">
            <button class="btn btn-success btn-check-all"> <?= __('display_all') ?> </button>
        </div>
    </div>
</div> 

<div class="filter-header d-flex hidden"> </div>

<style>
    .hidden {
        display: none;
    }

    .filter-squar {
        border: 1px solid var(--primary);
        background-color: lightblue;
        border-radius: 10px;
        height: 50px;       /* cho chu ko loi ra */
    }

    .filter-header {
        padding: 10px
    }
</style>

<script>
    $(document).ready(function() {
       
        let isShow = true;
        let headers = [];
        // let key = 'Product.filterColumns';
        let key = '<?= $key ?>'
        // localStorage.removeItem(key);

        let ths = document.querySelectorAll("thead tr th");
        let trs = document.querySelectorAll("tbody tr");

        // put on filterHeader;
        let btnFilterShowAll = document.querySelector(".btn-filter-show-all")
        let filterHeader = document.querySelector(".filter-header");

        function handling_init_data() {
            headers = [];
            let htmls = `<div class="row mt-2 mb-2">`;

            ths.forEach((value, index) => {
                // push headers to array;

                let a = value.querySelector('a')

                if (a && a.textContent != '<?= __('id'); ?>' && a.textContent != '<?= __('operation') ?>') {
                    headers.push({
                        name: a.textContent,
                        isChecked: 1
                    })
                }
            })

            let columns = [];
            headers.forEach((value, index) => {
                let name = value['name']
                let isChecked = value['isChecked'] === 1 ? 'checked' : ''

                htmls +=
                    `<div class='col-1 filter-squar'>
                        <input class="show-header" type="checkbox" ${isChecked} value="${name}"> ${name} </input>
                    </div>`

                show_hide_column(index, isChecked)

            })
            localStorage.setItem(key, JSON.stringify(headers));

            htmls += '</div>';
            filterHeader.innerHTML = htmls; // cannot use append , use innerHTML instead

        }

        function show_hide_column(index, isChecked) {
            _index = index + 1
           
            if (isChecked === 'checked') {
                ths[_index].classList.remove('hidden');

                // loop and hide all tds
                trs.forEach((tr, id) => {
                    let tds = tr.querySelectorAll("td");
                    tds[_index].classList.remove('hidden');
                }) 
               
            } else {
                ths[_index].classList.add('hidden');

                // loop and hide all tds
                trs.forEach((tr, id) => {
                    let tds = tr.querySelectorAll("td");
                    tds[_index].classList.add('hidden');
                }) 
            }
        }

        if (!localStorage.getItem(key)) { // no data;
            handling_init_data();

        } else { // exist localStorage ->  read from localStorage

            let htmls = `<div class="row mt-2 mb-2">`;
            headers = JSON.parse(localStorage.getItem(key));
            headers.forEach((value, index) => {
                const name = value['name'];
                const isChecked = (value['isChecked'] === 1) ? 'checked' : '';

                htmls +=
                    `<div class='col-1 filter-squar'>
                        <input class="show-header" type="checkbox" ${isChecked} value="${name}"> ${name} </input>
                    </div>`

                // load hide / show columns on view
                show_hide_column(index, isChecked)

            });
            htmls += '</div>';
            filterHeader.innerHTML = htmls; // cannot use append , use innerHTML instead

        }
        // click filter button
        
        $('#btn-filter').on('click', function() {
            filterHeader.classList.add('hidden');
            btnFilterShowAll.classList.add('hidden')
            if (isShow == true) {
                filterHeader.classList.remove('hidden');
                btnFilterShowAll.classList.remove('hidden')
            }

            isShow = !isShow;
        })

        $(document).on('change', '.show-header', function() {
            let trs = document.querySelectorAll("tbody tr");

            headers.forEach((value, index) => {
                if ($(this).prop('checked') === true) { // display
                    if (value['name'] == $(this).val()) {
                        _index = index + 1; // bypass id
                        ths[_index].classList.remove('hidden');

                        // loop all row and hide it (vilh)
                        trs.forEach((tr, id) => {
                            let tds = tr.querySelectorAll("td")
                            tds[_index].classList.remove('hidden');
                        })
                       
                        headers[index] = {
                            'name': value['name'],
                            'isChecked': 1
                        }

                    }
                } else { // hide
                    if (value['name'] == $(this).val()) {
                        _index = index + 1; // bypass id
                        ths[_index].classList.add('hidden');

                        trs.forEach((tr, id) => {
                            let tds = tr.querySelectorAll("td")
                            tds[_index].classList.add('hidden');
                        })
                      
                        headers[index] = {
                            'name': value['name'],
                            'isChecked': 0
                        }
                    }
                }
            })

            localStorage.setItem(key, JSON.stringify(headers));
        });

        //btn-check-all
        $('.btn-check-all').on('click', function() {
            handling_init_data()
        })
    });
</script>