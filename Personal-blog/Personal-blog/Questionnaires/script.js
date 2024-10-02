const countrySelect = document.getElementById('country');

fetch('https://restcountries.com/v3.1/all')
    .then(response => response.json())
    .then(data => {
        // 将国家列表按照首字母排序
        data.sort((a, b) => {
            const countryA = a.name.common.toUpperCase();
            const countryB = b.name.common.toUpperCase();
            if (countryA < countryB) {
                return -1;
            }
            if (countryA > countryB) {
                return 1;
            }
            return 0;
        });

        // 添加排序后的国家选项到选择框
        data.forEach(country => {
            const option = document.createElement('option');
            // 将国家名称从英文转换为中文
            const chineseName = getChineseName(country.name.common);
            option.value = chineseName;
            option.textContent = chineseName;
            countrySelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('获取国家列表时出错:', error);
    });

// 函数来获取中文国家名称
function getChineseName(englishName) {
    // 这里可以根据需要添加更多的国家名称对照
    switch(englishName) {
        case 'Afghanistan':
            return '阿富汗';
        case 'Albania':
            return '阿尔巴尼亚';
        case 'Algeria':
            return '阿尔及利亚';
        case 'Andorra':
            return '安道尔';
        case 'Angola':
            return '安哥拉';
        case 'Antigua and Barbuda':
            return '安提瓜和巴布达';
        case 'Argentina':
            return '阿根廷';
        case 'Armenia':
            return '亚美尼亚';
        case 'Australia':
            return '澳大利亚';
        case 'Austria':
            return '奥地利';
        case 'Azerbaijan':
            return '阿塞拜疆';
        case 'Bahamas':
            return '巴哈马';
        case 'Bahrain':
            return '巴林';
        case 'Bangladesh':
            return '孟加拉国';
        case 'Barbados':
            return '巴巴多斯';
        case 'Belarus':
            return '白俄罗斯';
        case 'Belgium':
            return '比利时';
        case 'Belize':
            return '伯利兹';
        case 'Benin':
            return '贝宁';
        case 'Bhutan':
            return '不丹';
        case 'Bolivia':
            return '玻利维亚';
        case 'Bosnia and Herzegovina':
            return '波斯尼亚和黑塞哥维那';
        case 'Botswana':
            return '博茨瓦纳';
        case 'Brunei':
            return '文莱';
        case 'Bulgaria':
            return '保加利亚';
        case 'Burkina Faso':
            return '布基纳法索';
        case 'Burundi':
            return '布隆迪';
        case 'Cabo Verde':
            return '佛得角';
        case 'Cambodia':
            return '柬埔寨';
        case 'Cameroon':
            return '喀麦隆';
        case 'Canada':
            return '加拿大';
        case 'Central African Republic':
            return '中非共和国';
        case 'Chad':
            return '乍得';
        case 'Chile':
            return '智利';
        case 'China':
            return '中国';
        case 'Colombia':
            return '哥伦比亚';
        case 'Comoros':
            return '科摩罗';
        case 'Congo (Congo-Brazzaville)':
            return '刚果（布）';
        case 'Costa Rica':
            return '哥斯达黎加';
        case 'Croatia':
            return '克罗地亚';
        case 'Cuba':
            return '古巴';
        case 'Cyprus':
            return '塞浦路斯';
        case 'Czechia (Czech Republic)':
            return '捷克';
        case 'Denmark':
            return '丹麦';
        case 'Djibouti':
            return '吉布提';
        case 'Dominica':
            return '多米尼加';
        case 'Dominican Republic':
            return '多米尼加共和国';
        case 'Ecuador':
            return '厄瓜多尔';
        case 'Egypt':
            return '埃及';
        case 'El Salvador':
            return '萨尔瓦多';
        case 'Equatorial Guinea':
            return '赤道几内亚';
        case 'Eritrea':
            return '厄立特里亚';
        case 'Estonia':
            return '爱沙尼亚';
        case 'Eswatini (fmr. "Swaziland")':
            return '斯威士兰';
        case 'Ethiopia':
            return '埃塞俄比亚';
        case 'Fiji':
            return '斐济';
        case 'Finland':
            return '芬兰';
        case 'France':
            return '法国';
        case 'Gabon':
            return '加蓬';
        case 'Gambia':
            return '冈比亚';
        case 'Georgia':
            return '格鲁吉亚';
        case 'Germany':
            return '德国';
        case 'Ghana':
            return '加纳';
        case 'Greece':
            return '希腊';
        case 'Grenada':
            return '格林纳达';
        case 'Guatemala':
            return '危地马拉';
        case 'Guinea':
            return '几内亚';
        case 'Guinea-Bissau':
            return '几内亚比绍';
        case 'Guyana':
            return '圭亚那';
        case 'Haiti':
            return '海地';
        case 'Holy See':
            return '梵蒂冈';
        case 'Honduras':
            return '洪都拉斯';
        case 'Hungary':
            return '匈牙利';
        case 'Iceland':
            return '冰岛';
        case 'India':
            return '印度';
        case 'Indonesia':
            return '印度尼西亚';
        case 'Iran':
            return '伊朗';
        case 'Iraq':
            return '伊拉克';
        case 'Ireland':
            return '爱尔兰';
        case 'Israel':
            return '以色列';
        case 'Italy':
            return '意大利';
        case 'Jamaica':
            return '牙买加';
        case 'Japan':
            return '日本';
        case 'Jordan':
            return '约旦';
        case 'Kazakhstan':
            return '哈萨克斯坦';
        case 'Kenya':
            return '肯尼亚';
        case 'Kiribati':
            return '基里巴斯';
        case 'Korea, North':
            return '朝鲜';
        case 'Korea, South':
            return '韩国';
        case 'Kosovo':
            return '科索沃';
        case 'Kuwait':
            return '科威特';
            case 'Kyrgyzstan':
            return '吉尔吉斯斯坦';
        case 'Laos':
            return '老挝';
        case 'Latvia':
            return '拉脱维亚';
        case 'Lebanon':
            return '黎巴嫩';
        case 'Lesotho':
            return '莱索托';
        case 'Liberia':
            return '利比里亚';
        case 'Libya':
            return '利比亚';
        case 'Liechtenstein':
            return '列支敦士登';
        case 'Lithuania':
            return '立陶宛';
        case 'Luxembourg':
            return '卢森堡';
        case 'Madagascar':
            return '马达加斯加';
        case 'Malawi':
            return '马拉维';
        case 'Malaysia':
            return '马来西亚';
        case 'Maldives':
            return '马尔代夫';
        case 'Mali':
            return '马里';
        case 'Malta':
            return '马耳他';
        case 'Marshall Islands':
            return '马绍尔群岛';
        case 'Mauritania':
            return '毛里塔尼亚';
        case 'Mauritius':
            return '毛里求斯';
        case 'Mexico':
            return '墨西哥';
        case 'Micronesia':
            return '密克罗尼西亚';
        case 'Moldova':
            return '摩尔多瓦';
        case 'Monaco':
            return '摩纳哥';
        case 'Mongolia':
            return '蒙古';
        case 'Montenegro':
            return '黑山';
        case 'Morocco':
            return '摩洛哥';
        case 'Mozambique':
            return '莫桑比克';
        case 'Myanmar (formerly Burma)':
            return '缅甸';
        case 'Namibia':
            return '纳米比亚';
        case 'Nauru':
            return '瑙鲁';
        case 'Nepal':
            return '尼泊尔';
        case 'Netherlands':
            return '荷兰';
        case 'New Zealand':
            return '新西兰';
        case 'Nicaragua':
            return '尼加拉瓜';
        case 'Niger':
            return '尼日尔';
        case 'Nigeria':
            return '尼日利亚';
        case 'North Macedonia':
            return '北马其顿';
        case 'Norway':
            return '挪威';
        case 'Oman':
            return '阿曼';
        case 'Pakistan':
            return '巴基斯坦';
        case 'Palau':
            return '帕劳';
        case 'Palestine State':
            return '巴勒斯坦';
        case 'Panama':
            return '巴拿马';
        case 'Papua New Guinea':
            return '巴布亚新几内亚';
        case 'Paraguay':
            return '巴拉圭';
        case 'Peru':
            return '秘鲁';
        case 'Philippines':
            return '菲律宾';
        case 'Poland':
            return '波兰';
        case 'Portugal':
            return '葡萄牙';
        case 'Qatar':
            return '卡塔尔';
        case 'Romania':
            return '罗马尼亚';
        case 'Russia':
            return '俄罗斯';
        case 'Rwanda':
            return '卢旺达';
        case 'Saint Kitts and Nevis':
            return '圣基茨和尼维斯';
        case 'Saint Lucia':
            return '圣卢西亚';
        case 'Saint Vincent and the Grenadines':
            return '圣文森特和格林纳丁斯';
        case 'Samoa':
            return '萨摩亚';
        case 'San Marino':
            return '圣马力诺';
        case 'Sao Tome and Principe':
            return '圣多美和普林西比';
        case 'Saudi Arabia':
            return '沙特阿拉伯';
        case 'Senegal':
            return '塞内加尔';
        case 'Serbia':
            return '塞尔维亚';
        case 'Seychelles':
            return '塞舌尔';
        case 'Sierra Leone':
            return '塞拉利昂';
        case 'Singapore':
            return '新加坡';
        case 'Slovakia':
            return '斯洛伐克';
        case 'Slovenia':
            return '斯洛文尼亚';
        case 'Solomon Islands':
            return '所罗门群岛';
        case 'Somalia':
            return '索马里';
        case 'South Africa':
            return '南非';
        case 'South Sudan':
            return '南苏丹';
        case 'Spain':
            return '西班牙';
        case 'Sri Lanka':
            return '斯里兰卡';
        case 'Sudan':
            return '苏丹';
        case 'Suriname':
            return '苏里南';
        case 'Sweden':
            return '瑞典';
        case 'Switzerland':
            return '瑞士';
        case 'Syria':
            return '叙利亚';
        case 'Tajikistan':
            return '塔吉克斯坦';
        case 'Tanzania':
            return '坦桑尼亚';
        case 'Thailand':
            return '泰国';
        case 'Timor-Leste':
            return '东帝汶';
        case 'Togo':
            return '多哥';
        case 'Tonga':
            return '汤加';
        case 'Trinidad and Tobago':
            return '特立尼达和多巴哥';
        case 'Tunisia':
            return '突尼斯';
        case 'Turkey':
            return '土耳其';
        case 'Turkmenistan':
            return '土库曼斯坦';
            case 'Tuvalu':
            return '图瓦卢';
        case 'Uganda':
            return '乌干达';
        case 'Ukraine':
            return '乌克兰';
        case 'United Arab Emirates':
            return '阿拉伯联合酋长国';
        case 'United Kingdom':
            return '英国';
        case 'Uruguay':
            return '乌拉圭';
        case 'Uzbekistan':
            return '乌兹别克斯坦';
        case 'Vanuatu':
            return '瓦努阿图';
        case 'Vatican City':
            return '梵蒂冈';
        case 'Venezuela':
            return '委内瑞拉';
        case 'Vietnam':
            return '越南';
        case 'Yemen':
            return '也门';
        case 'Zambia':
            return '赞比亚';
        case 'Zimbabwe':
            return '津巴布韦';
        case 'American Samoa':
            return '美属萨摩亚'
        default:
            return englishName; // 默认情况下返回原始的英文名称
    }
}


        document.getElementById('surveyForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const loadingDiv = document.getElementById('loading');
            const successMessageDiv = document.getElementById('successMessage');

            loadingDiv.style.display = 'block';
            successMessageDiv.style.display = 'none';

            fetch('submit.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                loadingDiv.style.display = 'none';
                if (result.message.includes("数据已存储")) {
                    successMessageDiv.style.display = 'block';
                    setTimeout(() => {
                        successMessageDiv.style.display = 'none';
                        form.reset();
                    }, 2000);
                } else {
                    alert(result.message);
                }
            })
            .catch(error => {
                console.error('错误:', error);
                loadingDiv.style.display = 'none';
                alert('提交失败，请稍后重试');
            });
        });