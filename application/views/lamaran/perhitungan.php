<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Data Ranking Recruitment</h1>
        </div>
        <?php
        // Matriks perbandingan berpasangan untuk AHP (contoh)
        $ahp_matrix = array(
            array(1, 3, 5),    // Pengalaman, Pendidikan, Keterampilan
            array(1 / 3, 1, 3),  // 1/nilai, 1, nilai
            array(1 / 5, 1 / 3, 1) // 1/nilai, 1/nilai, 1
        );


        // Normalisasi matriks AHP
        $normalized_ahp_matrix = array();
        foreach ($ahp_matrix as $key => $row) {
            $sum = array_sum($row);
            foreach ($row as $k => $value) {
                $normalized_ahp_matrix[$key][$k] = $value / $sum;
            }
        }

        // Hitung eigenvalue dan eigenvector dari matriks AHP
        $eigenvalues = array();
        foreach ($normalized_ahp_matrix as $key => $row) {
            $eigenvalues[$key] = array_sum($row) / count($row);
        }

        // Hitung bobot relatif dari eigenvector
        $sum_eigenvalues = array_sum($eigenvalues);
        $weights = array();
        foreach ($eigenvalues as $key => $value) {
            $weights[$key] = $value / $sum_eigenvalues;
        }
        
        $this->db->select('nama_lengkap,education,experience,skills');
        $this->db->from('tbl_lamaran');
        $query = $this->db->get();
        $array_real = $query->result_array();

        // Array untuk menyimpan data pelamar
        $applicants = array();

        // Iterasi melalui setiap baris hasil query
        foreach ($array_real as $row) {
            $applicants[] = array($row['nama_lengkap'],$row['education'], $row['experience'], $row['skills']);
        }
        $saw_values = array();
        foreach ($applicants as $key => $applicant) {
            $weighted_sum = 0;
            foreach ($applicant as $k => $value) {
                if ($k == 0) continue; // Skip nama pelamar
                $weighted_sum += $value * $weights[$k - 1];
            }
            $saw_values[$key] = array("nama_lengkap" => $applicant[0], "score" => $weighted_sum);
        }

        // Urutkan berdasarkan nilai terbesar
        usort($saw_values, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });


        ?>
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>List Data Ranking Recruitment Mengunakan Metode SPK & SAW</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h4 class="text-white">List Data Ranking Recruitment SAW</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-bordered">
                                                    <thead class="text-center">
                                                        <tr class="bg-primary">
                                                            <th class="text-white">Peringkat </th>
                                                            <th class="text-white">Nama Lengkap</th>
                                                            <th class="text-white">Total Score</th>
                                                            <!-- <th class="text-white">Aksi</th> -->
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-center">
                                                        <?php
                                                        foreach ($saw_values as $key => $value) { ?>
                                                            <tr>
                                                                <td>
                                                                    <?= 'Peringkat ' . ($key + 1) ?>
                                                                </td>
                                                                <td>
                                                                    <?= $value["nama_lengkap"] ?>
                                                                </td>
                                                                <td>
                                                                    <?= $value["score"] ?>
                                                                </td>
                                                            </tr>
                                                        <?php   } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>